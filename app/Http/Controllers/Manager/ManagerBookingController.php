<?php

namespace App\Http\Controllers\Manager;

use App\Models\Booking;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class ManagerBookingController extends ManagerController
{
    const PER_PAGE = 15;

    const STATUSES = [
        '' => 'All statuses',
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
    ];

    public function index(Request $request)
    {
        $statuses = self::STATUSES;

        $bookings = Booking::query()
            ->whereIn('hostel_id', $this->managedHostelIds())
            ->with(['hostel', 'room', 'user'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('booking_ref', 'like', '%' . $request->q . '%')
                        ->orWhere('student_name', 'like', '%' . $request->q . '%')
                        ->orWhere('email', 'like', '%' . $request->q . '%');
                });
            })
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('manager.bookings.index', compact('bookings', 'statuses'));
    }

    public function updateStatus(Booking $booking, Request $request, WhatsAppService $whatsapp)
    {
        abort_unless($this->managedHostelIds()->contains($booking->hostel_id), 403, 'You do not manage this hostel.');

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $newStatus = $validated['status'];

        $booking->update(['status' => $newStatus]);
        $booking->ensureOccupancyExpiry();

        $whatsapp->notifyStatusChange($booking, $newStatus);

        return back()->with('success', 'Booking ' . $booking->booking_ref . ' marked as ' . $newStatus . '.');
    }
}