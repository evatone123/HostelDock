<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookingToken extends Model
{
    use HasFactory;

    public const PRICE_GHS = 70.00;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_USED = 'used';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_FAILED = 'failed';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_USED => 'Used',
        self::STATUS_EXPIRED => 'Expired',
        self::STATUS_FAILED => 'Failed',
    ];

    protected $fillable = [
        'token_ref',
        'user_id',
        'amount',
        'status',
        'gateway',
        'paystack_reference',
        'paystack_transaction_ref',
        'used_by_booking_id',
        'paid_at',
        'used_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'paid_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function usedBy()
    {
        return $this->belongsTo(Booking::class, 'used_by_booking_id');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isUsed(): bool
    {
        return $this->status === self::STATUS_USED;
    }

    public static function activeFor($user)
    {
        return static::where('user_id', $user->id)
            ->where('status', self::STATUS_ACTIVE)
            ->orderByDesc('id')
            ->first();
    }

    public static function latestOpenFor($user)
    {
        return static::where('user_id', $user->id)
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_ACTIVE])
            ->orderByDesc('id')
            ->first();
    }

    public function activate($paystackTransactionRef = null): self
    {
        $this->status = self::STATUS_ACTIVE;
        $this->gateway = 'paystack';
        $this->paystack_transaction_ref = $paystackTransactionRef;
        $this->paid_at = $this->paid_at ?? now();
        $this->save();

        return $this;
    }

    public function consume(Booking $booking): self
    {
        $this->status = self::STATUS_USED;
        $this->used_by_booking_id = $booking->id;
        $this->used_at = now();
        $this->save();

        return $this;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public static function generateReference(): string
    {
        do {
            $ref = strtoupper('HDT-' . Str::random(8));
        } while (static::where('token_ref', $ref)->exists());

        return $ref;
    }

    public static function paystackReference(): string
    {
        do {
            $ref = strtoupper('PSK-' . Str::random(12));
        } while (static::where('paystack_reference', $ref)->exists());

        return $ref;
    }
}
