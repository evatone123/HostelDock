<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function profile()
    {
        return view('account.profile');
    }

    public function profileUpdate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:191',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = Auth::user();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function myBookings()
    {
        $bookings = Booking::with(['hostel.paymentDetail', 'room.bookings' => function ($query) {
            $query->where('status', 'confirmed');
        }])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('account.my-bookings', compact('bookings'));
    }

    public function cancelBooking(Booking $booking, WhatsAppService $whatsapp)
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        if ($booking->status === 'pending') {
            $booking->update(['status' => 'cancelled']);
            $whatsapp->notifyStatusChange($booking, 'cancelled');
        }

        return back()->with('success', 'Booking cancelled successfully.');
    }
}