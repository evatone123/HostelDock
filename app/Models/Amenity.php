<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_amenity');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}