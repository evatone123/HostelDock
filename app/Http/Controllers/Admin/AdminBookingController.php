<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
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

        return view('admin.bookings.index', compact('bookings', 'statuses'));
    }

    public function updateStatus(Booking $booking, Request $request, WhatsAppService $whatsapp)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $newStatus = $validated['status'];

        // A cancelled booking releases its bed automatically, since availability
        // is derived from live bookings rather than a stored counter.
        $booking->update(['status' => $newStatus]);
        $booking->ensureOccupancyExpiry();

        $whatsapp->notifyStatusChange($booking, $newStatus);

        return back()->with('success', 'Booking ' . $booking->booking_ref . ' marked as ' . $newStatus . '.');
    }
}