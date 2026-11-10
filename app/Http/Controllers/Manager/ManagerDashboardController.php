<?php

namespace App\Http\Controllers\Manager;

use App\Models\Booking;
use App\Models\Room;

class ManagerDashboardController extends ManagerController
{
    public function index()
    {
        $managedIds = $this->managedHostelIds();

        $stats = [
            'hostels' => count($managedIds),
            'rooms' => Room::whereIn('hostel_id', $managedIds)->count(),
            'bookings' => Booking::whereIn('hostel_id', $managedIds)->count(),
            'pending_bookings' => Booking::whereIn('hostel_id', $managedIds)->where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::whereIn('hostel_id', $managedIds)->where('status', 'confirmed')->count(),
            'revenue' => Booking::whereIn('hostel_id', $managedIds)->where('status', 'confirmed')->sum('amount'),
        ];

        $hostels = $this->managedHostels()
            ->withCount('rooms')
            ->withCount([
                'bookings as pending_bookings' => function ($q) {
                    $q->where('status', 'pending');
                },
            ])
            ->orderBy('name')
            ->get();

        $recentBookings = Booking::with(['hostel', 'room'])
            ->whereIn('hostel_id', $managedIds)
            ->latest()
            ->take(6)
            ->get();

        $lowRooms = Room::withAvailability()
            ->whereIn('hostel_id', $managedIds)
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

        return view('manager.dashboard', compact('stats', 'hostels', 'recentBookings', 'lowRooms'));
    }
}