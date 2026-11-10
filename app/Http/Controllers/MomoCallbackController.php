<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MomoCallbackController extends Controller
{
    public function handle(Request $request, WhatsAppService $whatsapp)
    {
        if (! $request->isJson()) {
            return response()->json(['ok' => false, 'error' => 'invalid payload'], 400);
        }

        $payload = $request->json()->all();

        Log::channel('daily')->info('MTN MoMo callback', ['payload' => $payload]);

        if (! is_array($payload)) {
            return response()->json(['ok' => false, 'error' => 'invalid payload'], 400);
        }

        $status = strtoupper((string) ($payload['status'] ?? ''));
        $externalId = (string) ($payload['externalId'] ?? '');
        $amount = (float) ($payload['amount'] ?? 0);
        $currency = strtoupper((string) ($payload['currency'] ?? config('mtnmomo.currency', 'GHS')));
        $transactionId = (string) ($payload['financialTransactionId'] ?? '');

        if ($externalId === '' || $status === '') {
            return response()->json(['ok' => false, 'error' => 'missing fields'], 400);
        }

        $booking = Booking::where('booking_ref', $externalId)
            ->orWhere('momo_request_reference', $externalId)
            ->first();

        if (! $booking) {
            Log::channel('daily')->warning('MTN MoMo callback for unknown booking', ['externalId' => $externalId]);

            return response()->json(['ok' => false, 'error' => 'unknown booking'], 404);
        }

        $booking->load('hostel.paymentDetail');

        if ($status !== 'SUCCESSFUL') {
            if ($status === 'FAILED' && $booking->isMomoPending()) {
                $booking->markFailed();
                $whatsapp->notifyPaymentResult($booking, false);
            }

            return response()->json(['ok' => true]);
        }

        if (abs((float) $booking->amount - $amount) > 0.01 || $currency !== config('mtnmomo.currency', 'GHS')) {
            Log::channel('daily')->warning('MTN MoMo amount mismatch', [
                'booking' => $booking->booking_ref,
                'expected' => $booking->amount,
                'received' => $amount,
            ]);

            return response()->json(['ok' => false, 'error' => 'amount mismatch'], 422);
        }

        if ($booking->isPaid()) {
            return response()->json(['ok' => true]);
        }

        $booking->markPaidVia(Booking::GATEWAY_MTN_MOMO, $transactionId);
        $booking->status = 'confirmed';
        $booking->save();

        $whatsapp->notifyPaymentResult($booking, true, $transactionId);
        $whatsapp->notifyHostelPaymentReceived($booking, $transactionId);

        return response()->json(['ok' => true]);
    }
}