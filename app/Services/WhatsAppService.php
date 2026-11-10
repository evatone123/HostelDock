<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingToken;
use App\Models\Hostel;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $enabled;

    protected $baseUrl;

    protected $apiKey;

    protected $sessionName;

    protected $adminAlertNumber;

    protected $business;

    public function __construct(WhatsAppBusinessApiService $business)
    {
        $this->enabled = (bool) config('services.whatsapp.enabled', false);
        $this->baseUrl = rtrim(config('services.whatsapp.base_url', 'http://127.0.0.1:2785/api'), '/');
        $this->apiKey = (string) config('services.whatsapp.api_key');
        $this->sessionName = (string) config('services.whatsapp.session_name', 'main');
        $this->adminAlertNumber = (string) config('services.whatsapp.admin_alert_number');
        $this->business = $business;
    }

    public function isEnabled(): bool
    {
        return $this->enabled
            && $this->apiKey !== ''
            && $this->sessionName !== ''
            && $this->baseUrl !== '';
    }

    public function normalizePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (! $digits || strlen($digits) < 9 || strlen($digits) > 15) {
            return null;
        }

        if (strlen($digits) === 10 && $digits[0] === '0') {
            return '233' . substr($digits, 1);
        }

        if (strlen($digits) === 12 && substr($digits, 0, 3) === '233') {
            return $digits;
        }

        return $digits;
    }

    /**
     * Send a WhatsApp text message. Best-effort: never throws, never blocks
     * the request when the gateway is unavailable or misconfigured.
     */
    public function send(string $phone, string $message): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        $digits = $this->normalizePhone($phone);

        if (! $digits) {
            return false;
        }

        if ($this->business->isActive()) {
            return $this->business->send($digits, $message);
        }

        $chatId = $digits . '@c.us';

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->withOptions(['timeout' => 8, 'verify' => false])->post($this->endpoint('messages/send-text'), [
                'chatId' => $chatId,
                'text' => $message,
            ]);

            if ($response->successful()) {
                return true;
            }

            \Log::warning('WhatsApp send failed', [
                'chatId' => $chatId,
                'status' => $response->status(),
                'body' => substr((string) $response->body(), 0, 500),
            ]);

            return false;
        } catch (\Throwable $e) {
            \Log::warning('WhatsApp send error: ' . $e->getMessage(), ['chatId' => $chatId]);
            return false;
        }
    }

    protected function endpoint(string $path): string
    {
        return $this->baseUrl . '/sessions/' . rawurlencode($this->sessionName) . '/' . $path;
    }

    public function notifyBookingReceived(Booking $booking): void
    {
        $name = $booking->student_name ?: '';
        $message = "Hello {$name},\n\n"
            . "Your room booking has been received.\n\n"
            . "Booking ref: {$booking->booking_ref}\n"
            . "Hostel: {$booking->hostel->displayName()}\n"
            . "Room: {$booking->room_type}\n"
            . "Amount due: GHS " . number_format($booking->amount, 2) . "\n\n"
            . "Your booking will be confirmed as soon as payment is received.\n\n"
            . "- HostelDock";

        $this->send($booking->phone, $message);
    }

    public function notifyHostelNewBooking(Booking $booking): void
    {
        $hostel = $booking->hostel;

        if (! $hostel) {
            return;
        }

        $message = "New booking received!\n\n"
            . "Ref: {$booking->booking_ref}\n"
            . "Student: {$booking->student_name} ({$booking->phone})\n"
            . "Room: {$booking->room_type}\n"
            . "Amount: GHS " . number_format($booking->amount, 2) . "\n\n"
            . "Review it in your dashboard.";

        $this->sendToHostelManager($hostel, $message);
    }

    public function notifyTokenActivated(BookingToken $token): void
    {
        $user = $token->user;

        if (! $user || ! $user->phone) {
            return;
        }

        $name = $user->name ?: '';
        $message = "Hello {$name},\n\n"
            . "Your booking access token {$token->token_ref} is now active.\n\n"
            . "You can now select a hostel and reserve your room. The token unlocks booking.\n\n"
            . "- HostelDock";

        $this->send($user->phone, $message);
    }

    public function notifyPaymentResult(Booking $booking, bool $success, ?string $transactionRef = null): void
    {
        if ($success) {
            $message = "Hello {$booking->student_name},\n\n"
                . "Payment received! GHS " . number_format($booking->amount, 2)
                . ($transactionRef ? " (transaction {$transactionRef})" : '') . "\n\n"
                . "Your booking {$booking->booking_ref} at {$booking->hostel->displayName()} is now CONFIRMED.\n\n"
                . "- HostelDock";
        } else {
            $message = "Hello {$booking->student_name},\n\n"
                . "We could not verify your payment for booking {$booking->booking_ref} (GHS " . number_format($booking->amount, 2) . ").\n\n"
                . "Please try again or contact the hostel for help.\n\n"
                . "- HostelDock";
        }

        $this->send($booking->phone, $message);
    }

    public function notifyHostelPaymentReceived(Booking $booking, ?string $transactionRef = null): void
    {
        $hostel = $booking->hostel;

        if (! $hostel) {
            return;
        }

        $message = "Payment received for booking {$booking->booking_ref}!\n\n"
            . "GHS " . number_format($booking->amount, 2) . " paid online"
            . ($transactionRef ? " (transaction {$transactionRef})" : '') . ".\n\n"
            . "The booking is now confirmed.";

        $this->sendToHostelManager($hostel, $message);
    }

    public function notifyStatusChange(Booking $booking, string $newStatus): void
    {
        $label = strtoupper($newStatus);

        $message = "Hello {$booking->student_name},\n\n"
            . "Update on your booking {$booking->booking_ref} at {$booking->hostel->displayName()}:\n\n"
            . "Status: {$label}.\n\n"
            . "- HostelDock";

        $this->send($booking->phone, $message);
    }

    public function notifyOccupancyRenewalWindow(Booking $booking): void
    {
        $expiry = $booking->expires_at ? $booking->expires_at->format('j M Y') : 'the expiry date';

        $message = "Hello {$booking->student_name},\n\n"
            . "Your occupancy at {$booking->hostel->displayName()} ({$booking->room_type}) ends on {$expiry}.\n\n"
            . "Your renewal window is open. Log in to HostelDock and open My Bookings to LOCK your room for renewal before it expires, then pay to extend your stay by another " . Booking::OCCUPANCY_DAYS . " days.\n\n"
            . "- HostelDock";

        $this->send($booking->phone, $message);
    }

    public function notifyRenewalResult(Booking $booking, bool $success, ?string $transactionRef = null): void
    {
        if ($success) {
            $message = "Hello {$booking->student_name},\n\n"
                . "Renewal payment received! Your occupancy at {$booking->hostel->displayName()} has been extended by " . Booking::OCCUPANCY_DAYS . " days"
                . ($transactionRef ? " (transaction {$transactionRef})" : '') . ".\n\n"
                . "New expiry date: " . ($booking->expires_at ? $booking->expires_at->format('j M Y') : 'N/A') . ".\n\n"
                . "- HostelDock";
        } else {
            $message = "Hello {$booking->student_name},\n\n"
                . "We could not verify your renewal payment for booking {$booking->booking_ref} at {$booking->hostel->displayName()}.\n\n"
                . "Please try again or contact the hostel for help.\n\n"
                . "- HostelDock";
        }

        $this->send($booking->phone, $message);
    }

    public function notifyAdminContact(array $contact): void
    {
        if (! $this->adminAlertNumber) {
            return;
        }

        $message = "New contact form message\n\n"
            . "From: {$contact['name']} ({$contact['email']})\n"
            . "Subject: {$contact['subject']}\n\n"
            . $contact['message'];

        $this->send($this->adminAlertNumber, $message);
    }

    protected function sendToHostelManager(Hostel $hostel, string $message): void
    {
        $phone = $hostel->managers()
            ->whereNotNull('users.phone')
            ->where('users.phone', '!=', '')
            ->value('users.phone');

        if (! $phone) {
            return;
        }

        $this->send($phone, $message);
    }

    public function sendToUser(User $user, string $message): void
    {
        if (! $user->phone) {
            return;
        }

        $this->send($user->phone, $message);
    }
}