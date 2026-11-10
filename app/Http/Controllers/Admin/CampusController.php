<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampusController extends Controller
{
    const PER_PAGE = 15;

    public function index(Request $request)
    {
        $campuses = Campus::withCount('hostels')
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%');
            })
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.campuses.index', compact('campuses'));
    }

    public function create()
    {
        return view('admin.campuses.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCampus($request);

        Campus::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'short_name' => $validated['short_name'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.campuses.index')->with('success', 'Campus added successfully.');
    }

    public function edit(Campus $campus)
    {
        return view('admin.campuses.edit', compact('campus'));
    }

    public function update(Request $request, Campus $campus)
    {
        $validated = $this->validateCampus($request);

        $previousName = $campus->name;

        $campus->fill([
            'name' => $validated['name'],
            'short_name' => $validated['short_name'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($previousName !== $validated['name']) {
            $campus->slug = $this->uniqueSlug($validated['name'], $campus->id);
        }

        $campus->save();

        return redirect()->route('admin.campuses.index')->with('success', 'Campus updated successfully.');
    }

    public function destroy(Campus $campus)
    {
        if ($campus->hostels()->exists()) {
            return back()->with('error', 'This campus is linked to hostels. Reassign those hostels first.');
        }

        $campus->delete();

        return back()->with('success', 'Campus deleted successfully.');
    }

    public function toggle(Campus $campus)
    {
        $campus->update(['is_active' => !$campus->is_active]);

        return back()->with('success', $campus->is_active
            ? 'Campus activated.'
            : 'Campus deactivated.');
    }

    protected function validateCampus(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:191',
            'short_name' => 'nullable|string|max:191',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'is_active' => 'nullable|boolean',
        ]);
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        while (Campus::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}