<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Concerns\ManagesHostelImages;
use App\Models\Hostel;
use Illuminate\Http\Request;

class ManagerHostelController extends ManagerController
{
    use ManagesHostelImages;

    public function index(Request $request)
    {
        $hostels = $this->managedHostels()
            ->withCount(['rooms', 'images', 'bookings'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%');
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('manager.hostels.index', compact('hostels'));
    }

    public function edit(Hostel $hostel)
    {
        $this->authorizeHostel($hostel);

        $thumbnails = $this->thumbnailOptions();
        $selected = $hostel->images()->pluck('image_path')->all();

        return view('manager.hostels.edit', compact('hostel', 'thumbnails', 'selected'));
    }

    public function update(Hostel $hostel, Request $request)
    {
        $this->authorizeHostel($hostel);

        $validated = $this->validateHostel($request);

        $hostel->fill([
            'name' => $validated['name'],
            'institution' => $validated['institution'] ?? null,
            'institution_name' => Hostel::institutions()[$validated['institution'] ?? ''] ?? null,
            'location' => $validated['location'],
            'distance' => $validated['distance'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'rating' => $validated['rating'] ?? 0,
            'description' => $validated['description'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($hostel->name !== $validated['name']) {
            $hostel->slug = $this->uniqueSlug($validated['name'], $hostel->id);
        }

        $hostel->save();
        $images = $this->mergeImages(
            $hostel,
            $request->file('images_upload', []),
            $request->input('images', [])
        );
        $this->syncImages($hostel, $images);
        $this->recalculateStartingPrice($hostel);

        return redirect()->route('manager.hostels.index')->with('success', 'Hostel updated successfully.');
    }

    protected function validateHostel(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:191',
            'institution' => 'nullable|string|max:191',
            'location' => 'required|string|max:191',
            'distance' => 'nullable|string|max:191',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'rating' => 'nullable|numeric|min:0|max:5',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'string|max:255',
            'images_upload' => 'nullable|array',
            'images_upload.*' => 'image|mimes:jpeg,png,webp,gif|max:5120',
        ]);
    }
}