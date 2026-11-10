<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendOccupancyRenewalReminders extends Command
{
    protected $signature = 'occupancy:renewal-reminders';

    protected $description = 'Remind confirmed occupants whose 365-day renewal window is open to lock and rebook.';

    public function handle(WhatsAppService $whatsapp)
    {
        $now = now();

        $bookings = Booking::with(['hostel', 'room'])
            ->where('status', 'confirmed')
            ->whereNull('renewal_window_notified_at')
            ->where('expires_at', '>', $now)
            ->where('expires_at', '<=', $now->copy()->addDays(Booking::RENEWAL_WINDOW_DAYS))
            ->get();

        foreach ($bookings as $booking) {
            $whatsapp->notifyOccupancyRenewalWindow($booking);

            if ($booking->email) {
                try {
                    Mail::send('emails.renewal-reminder', ['booking' => $booking], function ($message) use ($booking) {
                        $message->to($booking->email, $booking->student_name)
                            ->subject('Your hostel room renewal window is open');
                    });
                } catch (\Throwable $e) {
                    \Log::warning('Renewal reminder email failed', [
                        'booking_ref' => $booking->booking_ref,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $booking->renewal_window_notified_at = now();
            $booking->save();
        }

        $this->info('Renewal reminders sent to ' . $bookings->count() . ' booking(s).');

        return 0;
    }
}