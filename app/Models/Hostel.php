<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    use HasFactory;

    protected const ACRONYMS = ['KNUST', 'KSTU', 'UG', 'USTED', 'AAMUSTED', 'GIMPA', 'UCC', 'DK', 'SRC', 'GCTU'];

    protected $fillable = [
        'name',
        'slug',
        'institution',
        'institution_name',
        'location',
        'distance',
        'latitude',
        'longitude',
        'rating',
        'description',
        'starting_price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'float',
        'starting_price' => 'float',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function images()
    {
        return $this->hasMany(HostelImage::class)->orderBy('position');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function paymentDetail()
    {
        return $this->hasOne(HostelPaymentDetail::class);
    }

    public function managers()
    {
        return $this->belongsToMany(User::class, 'hostel_user')->withTimestamps();
    }

    public function coverImage()
    {
        return $this->hasOne(HostelImage::class)->orderBy('position');
    }

    public function sliderImages(): array
    {
        $images = array_values(array_filter((array) config('hosteldock.slider_images', [])));

        return $images ?: ['assets/images/about/slider-imgs.jpg'];
    }

    public function galleryImages(): array
    {
        $paths = [];

        foreach ($this->images as $image) {
            if (is_file(public_path($image->image_path))) {
                $paths[] = $image->image_path;
            }
        }

        return $paths ?: $this->sliderImages();
    }

    public function coverImagePath(): string
    {
        return $this->galleryImages()[0];
    }

    public function availableRooms()
    {
        return (int) $this->activeRooms()->sum(function ($room) {
            return $room->availableSpace();
        });
    }

    public function maleRooms()
    {
        return (int) $this->activeRooms()->where('gender_category', 'Male')->sum(function ($room) {
            return $room->availableSpace();
        });
    }

    public function femaleRooms()
    {
        return (int) $this->activeRooms()->where('gender_category', 'Female')->sum(function ($room) {
            return $room->availableSpace();
        });
    }

    public function unisexRooms()
    {
        return (int) $this->activeRooms()->where('gender_category', 'Unisex')->sum(function ($room) {
            return $room->availableSpace();
        });
    }

    protected function activeRooms()
    {
        if ($this->relationLoaded('rooms')) {
            return $this->rooms->where('is_active', true);
        }

        return $this->rooms()->active()->withAvailability()->get();
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function campusCoordinates(): ?array
    {
        if ($this->institution) {
            $campus = Campus::where('slug', $this->institution)->first();
            if ($campus) {
                return [
                    'name' => $campus->name,
                    'lat' => $campus->latitude,
                    'lng' => $campus->longitude,
                ];
            }
        }

        if ($this->institution_name) {
            return ['name' => $this->institution_name, 'lat' => null, 'lng' => null];
        }

        return null;
    }

    public static function institutions()
    {
        return ['' => 'Select Institution']
            + Campus::where('is_active', true)->orderBy('name')->pluck('name', 'slug')->all();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function displayName(): string
    {
        $name = trim(preg_replace('/\s*\([^)]*\)\s*$/', '', $this->name));

        return $this->titleCase($name);
    }

    public function institutionLabel(): ?string
    {
        if (preg_match('/\(([^)]+)\)\s*$/', $this->name, $matches)) {
            return $this->titleCase($matches[1]);
        }

        return $this->institution_name ? $this->titleCase($this->institution_name) : null;
    }

    protected function titleCase(string $value): string
    {
        $value = ucwords(strtolower(trim($value)));

        foreach (self::ACRONYMS as $acronym) {
            $value = preg_replace('/\b' . preg_quote(ucfirst(strtolower($acronym)), '/') . '\b/', $acronym, $value);
        }

        return $value;
    }
}