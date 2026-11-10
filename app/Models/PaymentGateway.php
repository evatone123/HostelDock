<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    public const TYPES = [
        'mobile_money' => 'Mobile Money',
        'bank' => 'Bank Transfer',
        'cash' => 'Cash at Hostel',
        'card' => 'Card / Online',
    ];

    protected $fillable = [
        'name',
        'type',
        'account_name',
        'account_number',
        'network',
        'instructions',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort');
    }
}