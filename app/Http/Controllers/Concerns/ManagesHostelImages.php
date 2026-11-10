<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Hostel;
use App\Models\HostelImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ManagesHostelImages
{
    protected function thumbnailOptions()
    {
        $files = [];
        $disk = Storage::disk('public');
        foreach ($disk->files('thumbnails') as $file) {
            $files['storage/' . $file] = basename($file);
        }
        asort($files);

        return $files;
    }

    protected function mergeImages(Hostel $hostel, array $uploads, array $library): array
    {
        $uploaded = $this->storeUploadedImages($hostel, $uploads);

        return array_merge(array_values($uploaded), array_values(array_filter($library)));
    }

    protected function storeUploadedImages(Hostel $hostel, array $files): array
    {
        $paths = [];
        $folder = 'hostels/' . $hostel->slug;

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $extension = $file->getClientOriginalExtension();
            $name = Str::random(24) . ($extension ? '.' . $extension : '');
            $stored = $file->storeAs($folder, $name, 'public_root');

            if ($stored) {
                $paths[] = 'uploads/' . $stored;
            }
        }

        return $paths;
    }

    protected function syncImages(Hostel $hostel, array $paths): void
    {
        $hostel->images()->delete();

        $positions = 0;
        foreach (array_values($paths) as $path) {
            HostelImage::create([
                'hostel_id' => $hostel->id,
                'image_path' => $path,
                'position' => $positions,
            ]);
            $positions++;
        }
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $count = 1;

        while (Hostel::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }

    public function recalculateStartingPrice(Hostel $hostel): void
    {
        $min = $hostel->rooms()->active()->min('price');
        $hostel->update(['starting_price' => $min ?: 0]);
    }
}