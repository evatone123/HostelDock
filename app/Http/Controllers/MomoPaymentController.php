<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\MTNMoMoService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class MomoPaymentController extends Controller
{
    public function pay(Booking $booking, Request $request, MTNMoMoService $momo)
    {
        abort_unless((int) $booking->user_id === (int) auth()->id(), 403);
        abort_unless($booking->isPayable(), 409);

        $booking->load('hostel.paymentDetail');
        $detail = $booking->hostel->paymentDetail;

        if (! $detail || ! $detail->hasMtnMoMoApi()) {
            abort(404);
        }

        if ($booking->isMomoPending()) {
            return back()->with('info', 'You already have a MoMo payment request pending. Approve it on your phone, then confirm the payment.');
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^(\+?233|0)[0-9]{9}$/', 'max:20'],
        ]);

        $response = $momo->requestToPay(
            $detail->momoApiCredentials(),
            (float) $booking->amount,
            $validated['phone'],
            $booking->booking_ref
        );

        if (! $response['ok']) {
            $booking->markFailed();

            return back()->withErrors(['payment' => $response['message']]);
        }

        $booking->markMomoPending($response['reference'], $validated['phone']);

        return redirect()
            ->route('booking.confirmation', $booking->booking_ref)
            ->with('info', 'A payment request has been sent to ' . $validated['phone'] . '. Approve it on your phone (via the MTN MoMo app or *170#), then confirm the payment.');
    }

    public function status(Booking $booking, MTNMoMoService $momo, WhatsAppService $whatsapp)
    {
        abort_unless((int) $booking->user_id === (int) auth()->id(), 403);

        $booking->load('hostel.paymentDetail');

        if (! $booking->isMomoPending() || ! $booking->hostel->paymentDetail) {
            return redirect()->route('booking.confirmation', $booking->booking_ref);
        }

        $response = $momo->getRequestToPayStatus(
            $booking->hostel->paymentDetail->momoApiCredentials(),
            $booking->momo_request_reference
        );

        $status = $response['status'] ?? '';

        if ($response['ok'] && $status === 'SUCCESSFUL') {
            $transactionRef = $response['financial_transaction_id'];

            $booking->markPaidVia(Booking::GATEWAY_MTN_MOMO, $transactionRef);
            $booking->status = 'confirmed';
            $booking->save();
            $booking->ensureOccupancyExpiry();

            $whatsapp->notifyPaymentResult($booking, true, $transactionRef);
            $whatsapp->notifyHostelPaymentReceived($booking, $transactionRef);

            return redirect()
                ->route('booking.confirmation', $booking->booking_ref)
                ->with('success', 'Payment received. Your booking is confirmed.');
        }

        if ($response['ok'] && $status === 'FAILED') {
            $booking->markFailed();
            $whatsapp->notifyPaymentResult($booking, false);

            return redirect()
                ->route('booking.confirmation', $booking->booking_ref)
                ->withErrors(['payment' => 'The MoMo payment failed. You can retry or use another method.']);
        }

        return back()->with('info', 'Your MoMo payment is still pending. Please approve it on your phone and try again.');
    }
}