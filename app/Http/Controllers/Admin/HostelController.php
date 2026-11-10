<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ManagesHostelImages;
use App\Http\Controllers\Controller;
use App\Models\Hostel;
use Illuminate\Http\Request;

class HostelController extends Controller
{
    use ManagesHostelImages;

    const PER_PAGE = 12;

    public function index(Request $request)
    {
        $hostels = Hostel::withCount(['rooms', 'images'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%');
            })
            ->when($request->filled('institution'), function ($q) use ($request) {
                $q->where('institution', $request->institution);
            })
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.hostels.index', compact('hostels'));
    }

    public function create()
    {
        $thumbnails = $this->thumbnailOptions();

        return view('admin.hostels.create', compact('thumbnails'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateHostel($request);

        $hostel = Hostel::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'institution' => $validated['institution'] ?? null,
            'institution_name' => Hostel::institutions()[$validated['institution'] ?? ''] ?? null,
            'location' => $validated['location'],
            'distance' => $validated['distance'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'rating' => $validated['rating'] ?? 0,
            'description' => $validated['description'],
            'starting_price' => 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        $images = $this->mergeImages(
            $hostel,
            $request->file('images_upload', []),
            $request->input('images', [])
        );
        $this->syncImages($hostel, $images);

        return redirect()->route('admin.hostels.index')->with('success', 'Hostel created. Add rooms to it next.');
    }

    public function edit(Hostel $hostel)
    {
        $thumbnails = $this->thumbnailOptions();
        $selected = $hostel->images()->pluck('image_path')->all();

        return view('admin.hostels.edit', compact('hostel', 'thumbnails', 'selected'));
    }

    public function update(Hostel $hostel, Request $request)
    {
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

        return redirect()->route('admin.hostels.index')->with('success', 'Hostel updated successfully.');
    }

    public function destroy(Hostel $hostel)
    {
        $hostel->delete(); // cascade removes rooms + images

        return back()->with('success', 'Hostel and its rooms deleted.');
    }

    public function toggle(Hostel $hostel)
    {
        $hostel->update(['is_active' => !$hostel->is_active]);

        return back()->with('success', $hostel->is_active
            ? 'Hostel activated and is visible on the website.'
            : 'Hostel deactivated and hidden from the website.');
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