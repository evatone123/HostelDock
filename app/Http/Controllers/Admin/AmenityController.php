<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AmenityController extends Controller
{
    const PER_PAGE = 15;

    const ICONS = [
        'bi-wifi' => 'Wi-Fi',
        'bi-shield-check' => 'Security',
        'bi-droplet-half' => 'Water supply',
        'bi-lightning-charge' => 'Electricity',
        'bi-house-door' => 'Furnished',
        'bi-bounding-box' => 'Spacious',
        'bi-cup-hot' => 'Water dispenser',
        'bi-parking' => 'Car parking',
        'bi-snow' => 'Air conditioning',
        'bi-fan' => 'Ceiling fan',
        'bi-tv' => 'TV',
        'bi-fridge' => 'Fridge',
        'bi-thermometer-half' => 'Heater',
        'bi-bucket' => 'Wardrobe / storage',
        'bi-door-closed' => 'Private entrance',
        'bi-tsunami' => 'Bed &amp; mattress',
        'bi-cup' => 'Kitchenette',
        'bi-washing-machine' => 'Laundry',
        'bi-plug' => 'Power sockets',
        'bi-grid-1x2' => 'Study desk',
        'bi-lamp' => 'Reading lamp',
        'bi-droplet' => 'En-suite bathroom',
        'bi-check2-circle' => 'Other',
    ];

    public function index(Request $request)
    {
        $amenities = Amenity::withCount('rooms')
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%');
            })
            ->orderBy('name')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.amenities.index', compact('amenities'));
    }

    public function create()
    {
        $icons = self::ICONS;

        return view('admin.amenities.create', compact('icons'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateAmenity($request);

        Amenity::create([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.amenities.index')->with('success', 'Amenity added successfully.');
    }

    public function edit(Amenity $amenity)
    {
        $icons = self::ICONS;

        return view('admin.amenities.edit', compact('amenity', 'icons'));
    }

    public function update(Request $request, Amenity $amenity)
    {
        $validated = $this->validateAmenity($request);

        $amenity->update([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.amenities.index')->with('success', 'Amenity updated successfully.');
    }

    public function destroy(Amenity $amenity)
    {
        if ($amenity->rooms()->exists()) {
            return back()->with('error', 'This amenity is assigned to rooms. Remove it from those rooms first.');
        }

        $amenity->delete();

        return back()->with('success', 'Amenity deleted successfully.');
    }

    public function toggle(Amenity $amenity)
    {
        $amenity->update(['is_active' => !$amenity->is_active]);

        return back()->with('success', $amenity->is_active
            ? 'Amenity activated.'
            : 'Amenity deactivated.');
    }

    protected function validateAmenity(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:191',
            'icon' => ['nullable', 'string', 'max:191', Rule::in(array_keys(self::ICONS))],
            'is_active' => 'nullable|boolean',
        ]);
    }
}