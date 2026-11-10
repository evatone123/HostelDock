<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingToken;
use App\Models\Hostel;
use App\Models\HostelPaymentDetail;
use App\Models\Room;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $bookingTokenRevenue = BookingToken::whereIn('status', [BookingToken::STATUS_ACTIVE, BookingToken::STATUS_USED])->sum('amount');
        $bookingTokenCount = BookingToken::whereIn('status', [BookingToken::STATUS_ACTIVE, BookingToken::STATUS_USED])->count();
        $confirmedBookingsRevenue = Booking::where('status', 'confirmed')->sum('amount');
        $totalRevenue = $bookingTokenRevenue + $confirmedBookingsRevenue;

        $hostelGatewayCount = HostelPaymentDetail::where('is_active', true)
            ->where(function ($q) {
                $q->where('paystack_enabled', true)
                    ->orWhere('momo_api_enabled', true);
            })
            ->count();

        $stats = [
            'hostels' => Hostel::count(),
            'rooms' => Room::count(),
            'bookings' => Booking::count(),
            'users' => User::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'hostel_gateways_configured' => $hostelGatewayCount,
            'booking_token_revenue' => $bookingTokenRevenue,
            'booking_token_count' => $bookingTokenCount,
            'confirmed_bookings_revenue' => $confirmedBookingsRevenue,
            'total_revenue' => $totalRevenue,
            'revenue' => $confirmedBookingsRevenue,
        ];

        $recentBookings = Booking::with(['hostel', 'room'])
            ->latest()
            ->take(6)
            ->get();

        $recentTokenPayments = BookingToken::with('user')
            ->whereIn('status', [BookingToken::STATUS_ACTIVE, BookingToken::STATUS_USED])
            ->latest('paid_at')
            ->take(6)
            ->get();

        $lowRooms = Room::withAvailability()
            ->with('hostel')
            ->get()
            ->filter(function ($room) {
                return $room->availableSpace() < 5 && $room->availableSpace() < $room->totalBeds();
            })
            ->sortBy(function ($room) {
                return $room->availableSpace();
            })
            ->take(8)
            ->values();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'recentTokenPayments', 'lowRooms'));
    }
}