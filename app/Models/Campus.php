<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'short_name',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function hostels()
    {
        return $this->hasMany(Hostel::class, 'institution', 'slug');
    }

    public function displayName(): string
    {
        return $this->short_name ? $this->short_name . ' (' . $this->name . ')' : $this->name;
    }
}