<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Concerns\ManagesHostelImages;
use App\Models\Amenity;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ManagerRoomController extends ManagerController
{
    use ManagesHostelImages;

    const GENDERS = ['Unisex' => 'Unisex', 'Male' => 'Male', 'Female' => 'Female'];

    public function index(Hostel $hostel)
    {
        $this->authorizeHostel($hostel);

        $rooms = $hostel->rooms()
            ->with(['bookings' => function ($query) {
                $query->where('status', '!=', 'cancelled')->latest();
            }])
            ->orderBy('room_type')
            ->get();

        $hostel->setRelation('rooms', $rooms);

        return view('manager.rooms.index', compact('hostel', 'rooms'));
    }

    public function create(Hostel $hostel)
    {
        $this->authorizeHostel($hostel);

        $roomTypes = RoomCategory::options();
        $genders = self::GENDERS;
        $amenities = Amenity::active()->orderBy('name')->get();

        return view('manager.rooms.create', compact('hostel', 'roomTypes', 'genders', 'amenities'));
    }

    public function store(Hostel $hostel, Request $request)
    {
        $this->authorizeHostel($hostel);

        $validated = $this->validateRoom($request);
        $validated['is_active'] = $request->boolean('is_active');

        $room = Room::create($validated + ['hostel_id' => $hostel->id]);
        $room->amenities()->sync($validated['amenities'] ?? []);

        $this->recalculateStartingPrice($hostel);

        return redirect()->route('manager.hostels.rooms.index', $hostel)
            ->with('success', 'Room added successfully.');
    }

    public function edit(Hostel $hostel, Room $room)
    {
        $this->authorizeHostel($hostel);
        abort_unless((int) $room->hostel_id === (int) $hostel->id, 404);

        $roomTypes = $this->editableRoomTypes($room);
        $genders = self::GENDERS;
        $amenities = Amenity::active()->orderBy('name')->get();
        $selected = $room->amenities()->pluck('amenity_id')->all();

        return view('manager.rooms.edit', compact('hostel', 'room', 'roomTypes', 'genders', 'amenities', 'selected'));
    }

    public function update(Hostel $hostel, Room $room, Request $request)
    {
        $this->authorizeHostel($hostel);
        abort_unless((int) $room->hostel_id === (int) $hostel->id, 404);

        $validated = $this->validateRoom($request);
        $validated['is_active'] = $request->boolean('is_active');

        $room->update($validated);
        $room->amenities()->sync($validated['amenities'] ?? []);

        $this->recalculateStartingPrice($hostel);

        return redirect()->route('manager.hostels.rooms.index', $hostel)
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Hostel $hostel, Room $room)
    {
        $this->authorizeHostel($hostel);
        abort_unless((int) $room->hostel_id === (int) $hostel->id, 404);

        $room->delete();
        $this->recalculateStartingPrice($hostel);

        return back()->with('success', 'Room deleted successfully.');
    }

    public function toggle(Hostel $hostel, Room $room)
    {
        $this->authorizeHostel($hostel);
        abort_unless((int) $room->hostel_id === (int) $hostel->id, 404);

        $room->update(['is_active' => !$room->is_active]);
        $this->recalculateStartingPrice($hostel);

        return back()->with('success', $room->is_active ? 'Room activated.' : 'Room deactivated.');
    }

    /**
     * Active categories, plus the room's current category when that has since
     * been deactivated, so saving a room never silently reassigns it.
     */
    protected function editableRoomTypes(Room $room): array
    {
        $roomTypes = RoomCategory::options();

        if (! array_key_exists($room->room_type, $roomTypes)) {
            $roomTypes[$room->room_type] = $room->room_type . ' (hidden)';
        }

        return $roomTypes;
    }

    protected function validateRoom(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:191',
            'room_type' => ['required', 'string', 'max:191', Rule::exists('room_categories', 'name')],
            'gender_category' => 'required|string|max:191',
            'price' => 'required|numeric|min:0',
            'total_rooms' => 'required|integer|min:1|max:10000',
            'is_active' => 'nullable|boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);
    }
}