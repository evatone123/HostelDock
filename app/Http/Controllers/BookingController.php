<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingToken;
use App\Models\Hostel;
use App\Models\Room;
use App\Services\PaystackService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function create(Hostel $hostel, Request $request)
    {
        abort_unless($hostel->is_active, 404);

        $hostel->load([
            'rooms.amenities',
            'rooms.bookings' => function ($query) {
                $query->where('status', 'confirmed')->latest();
            },
            'rooms' => function ($query) {
                $query->withAvailability();
            },
        ]);

        $activeRooms = $hostel->rooms
            ->where('is_active', true)
            ->sortBy('capacity');

        $room = null;
        if ($request->filled('room')) {
            $room = $activeRooms->first(function ($r) use ($request) {
                return (int) $r->id === (int) $request->input('room')
                    && $r->availableSpace() > 0;
            });
        }

        $occupants = $room ? $room->bookings : collect();

        $roomsByType = $activeRooms->groupBy('room_type');

        $types = $roomsByType->keys()->values();

        // A category only counts as selected if this hostel actually has it,
        // otherwise a stale or hand-edited query string would hide every room.
        $selectedType = $request->input('room_type');

        if (! in_array($selectedType, $types->all(), true)) {
            $selectedType = $room ? $room->room_type : null;
        }

        return view('pages.booking', compact('hostel', 'roomsByType', 'types', 'selectedType', 'room', 'occupants'));
    }

    public function store(Hostel $hostel, Request $request, WhatsAppService $whatsapp)
    {
        abort_unless($hostel->is_active, 404);

        $validated = $request->validate([
            'room' => 'required|exists:rooms,id',
            'student_name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'required|string|max:191',
            'institution' => 'nullable|string|max:191',
            'gender' => 'nullable|string|max:191',
            'academic_year' => 'nullable|string|max:191',
        ]);

        $room = Room::where('id', $validated['room'])
            ->where('hostel_id', $hostel->id)
            ->where('is_active', true)
            ->firstOrFail();

        if ($room->availableSpace() < 1) {
            throw ValidationException::withMessages([
                'room' => 'Sorry, that room is no longer available. Please choose another room.',
            ]);
        }

        $bookingRef = $this->generateReference();

        $booking = Booking::create([
            'booking_ref' => $bookingRef,
            'user_id' => auth()->id(),
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'student_name' => $validated['student_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'institution' => $validated['institution'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'room_type' => $room->room_type,
            'academic_year' => $validated['academic_year'] ?? null,
            'amount' => $room->price,
            'status' => 'pending',
        ]);

        $token = BookingToken::activeFor(auth()->user());

        if ($token) {
            $token->consume($booking);
        }

        $whatsapp->notifyBookingReceived($booking);
        $whatsapp->notifyHostelNewBooking($booking);

        return redirect()
            ->route('booking.confirmation', $booking->booking_ref)
            ->with('success', 'Booking received. Your booking token has been used. Confirm payment to finalise your reservation.');
    }

    public function confirmation(Booking $booking)
    {
        $booking->load(['hostel.paymentDetail', 'room']);

        $roommates = $booking->room
            ? $booking->room->bookings()
                ->where('hostel_id', $booking->hostel_id)
                ->where('status', 'confirmed')
                ->where('id', '!=', $booking->id)
                ->latest()
                ->get()
            : collect();

        return view('pages.booking-confirmation', compact('booking', 'roommates'));
    }

    public function invoice(Booking $booking)
    {
        $booking->load(['hostel.paymentDetail', 'room']);
        return view('pages.booking-invoice', compact('booking'));
    }

    public function pay(Booking $booking, Request $request, PaystackService $paystack)
    {
        abort_unless((int) $booking->user_id === (int) auth()->id(), 403);
        abort_unless($booking->isPayable(), 409);

        $booking->load('hostel.paymentDetail');

        if (! $booking->hostel->paymentDetail || ! $booking->hostel->paymentDetail->hasPaystack()) {
            abort(404);
        }

        $pay = $booking->hostel->paymentDetail;

        $paystackReference = Booking::paystackReference();
        $booking->paystack_reference = $paystackReference;
        $booking->save();

        $response = $paystack->initialize(
            $booking->email,
            $paystackReference,
            (float) $booking->amount,
            [
                'booking_ref' => $booking->booking_ref,
                'hostel' => $booking->hostel->name,
                'custom_fields' => [
                    [
                        'display_name' => 'Booking Ref',
                        'variable_name' => 'booking_ref',
                        'value' => $booking->booking_ref,
                    ],
                ],
            ],
            $pay->paystack_secret_key,
            route('booking.payment.callback')
        );

        if (! $response['ok']) {
            $booking->markFailed();
            return back()->withErrors(['payment' => $response['message']]);
        }

        return redirect()->away($response['authorization_url']);
    }

    public function paymentCallback(Request $request, PaystackService $paystack, WhatsAppService $whatsapp)
    {
        $reference = $request->query('reference');

        if (! $reference) {
            return redirect()->route('home')->withErrors(['payment' => 'We could not identify that payment. Please contact the hostel.']);
        }

        $renewalBooking = Booking::where('renewal_paystack_reference', $reference)
            ->with('hostel.paymentDetail')
            ->first();

        if ($renewalBooking) {
            return $this->completeRenewalPayment($renewalBooking, $reference, $paystack, $whatsapp);
        }

        $booking = Booking::where('paystack_reference', $reference)
            ->with('hostel.paymentDetail')
            ->first();

        if (! $booking) {
            return redirect()->route('home')->withErrors(['payment' => 'We could not find a booking for that payment. Please contact the hostel.']);
        }

        if ($booking->isPaid()) {
            return redirect()->route('booking.confirmation', $booking->booking_ref);
        }

        $secretKey = $booking->hostel->paymentDetail
            ? $booking->hostel->paymentDetail->paystack_secret_key
            : null;

        $response = $paystack->verify($reference, $secretKey);

        if ($response['ok'] && ($response['status'] ?? '') === 'success') {
            $booking->markPaid($response['transaction_ref']);
            $booking->status = 'confirmed';
            $booking->save();
            $booking->ensureOccupancyExpiry();

            $whatsapp->notifyPaymentResult($booking, true, $response['transaction_ref'] ?? null);
            $whatsapp->notifyHostelPaymentReceived($booking, $response['transaction_ref'] ?? null);

            return redirect()
                ->route('booking.confirmation', $booking->booking_ref)
                ->with('success', 'Payment received. Your booking is confirmed.');
        }

        $booking->markFailed();

        $whatsapp->notifyPaymentResult($booking, false);

        return redirect()
            ->route('booking.confirmation', $booking->booking_ref)
            ->withErrors(['payment' => $response['message'] ?? 'The payment could not be verified.']);
    }

    protected function completeRenewalPayment(Booking $booking, string $reference, PaystackService $paystack, WhatsAppService $whatsapp)
    {
        if ($booking->renewal_paystack_transaction_ref !== null) {
            return redirect()
                ->route('booking.confirmation', $booking->booking_ref)
                ->with('success', 'Renewal payment received. Your occupancy has been extended.');
        }

        $secretKey = $booking->hostel->paymentDetail
            ? $booking->hostel->paymentDetail->paystack_secret_key
            : null;

        $response = $paystack->verify($reference, $secretKey);

        if ($response['ok'] && ($response['status'] ?? '') === 'success') {
            $transactionRef = $response['transaction_ref'] ?? null;

            $booking->applyRenewal($transactionRef, Booking::GATEWAY_PAYSTACK);

            $whatsapp->notifyRenewalResult($booking, true, $transactionRef);

            return redirect()
                ->route('booking.confirmation', $booking->booking_ref)
                ->with('success', 'Renewal payment received. Your occupancy has been extended by ' . Booking::OCCUPANCY_DAYS . ' days.');
        }

        $whatsapp->notifyRenewalResult($booking, false);

        return redirect()
            ->route('booking.confirmation', $booking->booking_ref)
            ->withErrors(['payment' => $response['message'] ?? 'The renewal payment could not be verified.']);
    }

    protected function generateReference(): string
    {
        do {
            $ref = strtoupper('HSD-' . Str::random(8));
        } while (Booking::where('booking_ref', $ref)->exists());

        return $ref;
    }
}