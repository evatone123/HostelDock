<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelImage extends Model
{
    use HasFactory;

    protected $fillable = ['hostel_id', 'image_path', 'position'];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }
}