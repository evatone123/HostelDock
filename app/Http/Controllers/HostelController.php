<?php

namespace App\Http\Controllers;

use App\Models\Hostel;

class HostelController extends Controller
{
    public function show(Hostel $hostel)
    {
        abort_unless($hostel->is_active, 404);

        $hostel->load([
            'images',
            'rooms.amenities',
            'rooms' => function ($query) {
                $query->withAvailability();
            },
        ]);

        $roomsByType = $hostel->rooms
            ->where('is_active', true)
            ->sortBy('capacity')
            ->groupBy('room_type');

        $types = $roomsByType->keys()->values();

        $similar = Hostel::with(['images'])
            ->where('id', '!=', $hostel->id)
            ->where('is_active', true)
            ->where('institution', $hostel->institution)
            ->limit(3)
            ->get();

        return view('pages.hostel-detail', compact('hostel', 'roomsByType', 'types', 'similar'));
    }
}