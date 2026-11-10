<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\MTNMoMoService;
use App\Services\PaystackService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class BookingRenewalController extends Controller
{
    public function lock(Booking $booking)
    {
        abort_unless((int) $booking->user_id === (int) auth()->id(), 403);
        abort_unless($booking->status === 'confirmed', 409);
        abort_unless($booking->isWithinRenewalWindow(), 409, 'You can only lock for renewal within ' . Booking::RENEWAL_WINDOW_DAYS . ' days before your occupancy ends.');

        if ($booking->isRenewalLocked()) {
            return back()->with('info', 'This room is already locked for your renewal.');
        }

        $booking->lockForRenewal();

        return back()->with('success', 'Your room is locked for renewal. Pay before your occupancy ends to extend it.');
    }

    public function paystack(Booking $booking, PaystackService $paystack)
    {
        abort_unless((int) $booking->user_id === (int) auth()->id(), 403);
        $this->assertRenewableForPayment($booking);

        $booking->load('hostel.paymentDetail');

        if (! $booking->hostel->paymentDetail || ! $booking->hostel->paymentDetail->hasPaystack()) {
            abort(404);
        }

        $reference = Booking::renewalPaystackReference();
        $booking->startRenewalPaystack($reference);

        $response = $paystack->initialize(
            $booking->email,
            $reference,
            $booking->renewalPrice(),
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
            $booking->hostel->paymentDetail->paystack_secret_key,
            route('booking.payment.callback')
        );

        if (! $response['ok']) {
            return back()->withErrors(['payment' => $response['message']]);
        }

        return redirect()->away($response['authorization_url']);
    }

    public function momoPay(Booking $booking, Request $request, MTNMoMoService $momo)
    {
        abort_unless((int) $booking->user_id === (int) auth()->id(), 403);
        $this->assertRenewableForPayment($booking);

        $booking->load('hostel.paymentDetail');
        $detail = $booking->hostel->paymentDetail;

        if (! $detail || ! $detail->hasMtnMoMoApi()) {
            abort(404);
        }

        if ($booking->isRenewalMomoPending()) {
            return back()->with('info', 'You already have a renewal payment request pending. Approve it on your phone, then confirm the payment.');
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^(\+?233|0)[0-9]{9}$/', 'max:20'],
        ]);

        $response = $momo->requestToPay(
            $detail->momoApiCredentials(),
            $booking->renewalPrice(),
            $validated['phone'],
            $booking->booking_ref . '-R' . ((int) $booking->renewal_count + 1)
        );

        if (! $response['ok']) {
            return back()->withErrors(['payment' => $response['message']]);
        }

        $booking->startRenewalMomo($response['reference'], $validated['phone']);

        return redirect()
            ->route('booking.confirmation', $booking->booking_ref)
            ->with('info', 'A renewal payment request has been sent to ' . $validated['phone'] . '. Approve it on your phone, then confirm the payment.');
    }

    public function momoStatus(Booking $booking, MTNMoMoService $momo, WhatsAppService $whatsapp)
    {
        abort_unless((int) $booking->user_id === (int) auth()->id(), 403);

        $booking->load('hostel.paymentDetail');

        if (! $booking->isRenewalMomoPending() || ! $booking->hostel->paymentDetail) {
            return redirect()->route('booking.confirmation', $booking->booking_ref);
        }

        $response = $momo->getRequestToPayStatus(
            $booking->hostel->paymentDetail->momoApiCredentials(),
            $booking->renewal_momo_reference
        );

        $status = $response['status'] ?? '';

        if ($response['ok'] && $status === 'SUCCESSFUL') {
            $transactionRef = $response['financial_transaction_id'];

            $booking->applyRenewal($transactionRef, Booking::GATEWAY_MTN_MOMO);

            $whatsapp->notifyRenewalResult($booking, true, $transactionRef);

            return redirect()
                ->route('booking.confirmation', $booking->booking_ref)
                ->with('success', 'Renewal payment received. Your occupancy has been extended by ' . Booking::OCCUPANCY_DAYS . ' days.');
        }

        if ($response['ok'] && $status === 'FAILED') {
            $booking->clearRenewalMomoPending();
            $whatsapp->notifyRenewalResult($booking, false);

            return redirect()
                ->route('booking.confirmation', $booking->booking_ref)
                ->withErrors(['payment' => 'The renewal payment failed. You can retry or use another method.']);
        }

        return back()->with('info', 'Your renewal payment is still pending. Please approve it on your phone and try again.');
    }

    protected function assertRenewableForPayment(Booking $booking): void
    {
        abort_unless($booking->status === 'confirmed', 409);
        abort_unless($booking->isRenewalLocked(), 409, 'Lock your room for renewal before paying.');
        abort_unless($booking->isWithinRenewalWindow(), 409, 'The renewal window has closed. Your occupancy has ended.');
    }
}