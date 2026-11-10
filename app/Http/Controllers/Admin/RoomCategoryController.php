<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomCategoryController extends Controller
{
    const PER_PAGE = 15;

    public function index(Request $request)
    {
        $categories = RoomCategory::withCount('rooms')
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%');
            })
            ->ordered()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.room-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.room-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);

        $category = RoomCategory::create([
            'name' => $validated['name'],
            'capacity' => $validated['capacity'],
            'sort_order' => $validated['sort_order'] ?? $validated['capacity'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->propagateCapacity($category);

        return redirect()->route('admin.room-categories.index')
            ->with('success', 'Room category added successfully.');
    }

    public function edit(RoomCategory $roomCategory)
    {
        return view('admin.room-categories.edit', compact('roomCategory'));
    }

    public function update(Request $request, RoomCategory $roomCategory)
    {
        $validated = $this->validateCategory($request, $roomCategory);

        if ((int) $roomCategory->capacity !== $validated['capacity']) {
            $blocked = $this->blockedByOccupancy($roomCategory, $validated['capacity']);

            if ($blocked) {
                return back()
                    ->withInput()
                    ->with('error', $blocked);
            }
        }

        $roomCategory->update([
            'name' => $validated['name'],
            'capacity' => $validated['capacity'],
            'sort_order' => $validated['sort_order'] ?? $validated['capacity'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->propagateCapacity($roomCategory);

        return redirect()->route('admin.room-categories.index')
            ->with('success', 'Room category updated successfully.');
    }

    public function destroy(RoomCategory $roomCategory)
    {
        if ($roomCategory->rooms()->exists()) {
            return back()->with('error', 'This category is applied to rooms. Reassign those rooms first.');
        }

        $roomCategory->delete();

        return back()->with('success', 'Room category deleted successfully.');
    }

    public function toggle(RoomCategory $roomCategory)
    {
        $roomCategory->update(['is_active' => !$roomCategory->is_active]);

        return back()->with('success', $roomCategory->is_active
            ? 'Room category activated.'
            : 'Room category deactivated. Existing rooms keep their assignment.');
    }

    protected function propagateCapacity(RoomCategory $category): void
    {
        Room::where('room_type', $category->name)
            ->update(['capacity' => $category->capacity]);
    }

    protected function blockedByOccupancy(RoomCategory $category, int $capacity): ?string
    {
        $rooms = $category->rooms()->withAvailability()->get();

        foreach ($rooms as $room) {
            $occupied = (int) $room->pending_count + (int) $room->occupant_count;

            if ($occupied > ((int) $room->total_rooms * $capacity)) {
                return sprintf(
                    'Cannot reduce "%1$s" to %2$d per room: "%3$s" already has %4$d booked of %5$d total. Move or cancel those bookings first.',
                    $category->name,
                    $capacity,
                    $room->name,
                    $occupied,
                    (int) $room->total_rooms * $capacity
                );
            }
        }

        return null;
    }

    protected function validateCategory(Request $request, ?RoomCategory $category = null): array
    {
        $unique = Rule::unique('room_categories', 'name');

        if ($category !== null) {
            $unique->ignore($category->id);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:191', $unique],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
