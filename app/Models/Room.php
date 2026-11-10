<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'name',
        'room_type',
        'capacity',
        'gender_category',
        'price',
        'total_rooms',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function roomCategory()
    {
        return $this->belongsTo(RoomCategory::class, 'room_type', 'name');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'room_amenity');
    }

    /**
     * Capacity is owned by the room category, so keep the denormalized copy in
     * step on every write. Rooms whose category has since been deleted keep the
     * capacity they were last given.
     */
    protected static function booted(): void
    {
        static::saving(function (self $room) {
            $capacity = RoomCategory::where('name', $room->room_type)->value('capacity');

            if ($capacity !== null) {
                $room->capacity = (int) $capacity;
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Eager-load the counts needed to compute real availability.
     */
    public function scopeWithAvailability($query)
    {
        $query->withCount([
            'bookings as pending_count' => function ($q) {
                $q->where('status', 'pending');
            },
            'bookings as occupant_count' => function ($q) {
                $q->where('status', 'confirmed')
                    ->where(function ($q2) {
                        $q2->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                    });
            },
        ]);
    }

    /**
     * True when this room-type still has at least one free bed. Portable
     * across MySQL (production) and SQLite (tests).
     */
    public function scopeHasAvailability($query)
    {
        $query->whereRaw(
            "(rooms.total_rooms * rooms.capacity - (SELECT COUNT(*) FROM bookings WHERE bookings.room_id = rooms.id AND bookings.status = 'confirmed' AND (bookings.expires_at IS NULL OR bookings.expires_at >= CURRENT_TIMESTAMP)) - (SELECT COUNT(*) FROM bookings WHERE bookings.room_id = rooms.id AND bookings.status = 'pending')) > 0"
        );
    }

    /**
     * Beds this room-type can hold, derived from the category capacity.
     */
    public function totalBeds(): int
    {
        return (int) $this->total_rooms * max(1, (int) $this->capacity);
    }

    /**
     * Free beds = total beds minus confirmed occupants (active or indefinite)
     * minus pending reservation holds.
     */
    public function availableSpace(): int
    {
        return max(0, $this->totalBeds() - $this->occupiedSpace());
    }

    /**
     * Beds already spoken for by live bookings.
     */
    public function occupiedSpace(): int
    {
        if (array_key_exists('occupant_count', $this->getAttributes())) {
            return (int) $this->pending_count + (int) $this->occupant_count;
        }

        if ($this->relationLoaded('bookings')) {
            $pending = $this->bookings->where('status', 'pending')->count();
            $occupants = $this->bookings->where('status', 'confirmed')
                ->filter(function ($booking) {
                    return $booking->expires_at === null || $booking->expires_at->gte(now());
                })
                ->count();

            return $pending + $occupants;
        }

        $occupants = $this->bookings()
            ->where('status', 'confirmed')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->count();

        $pending = $this->bookings()->where('status', 'pending')->count();

        return $pending + $occupants;
    }
}