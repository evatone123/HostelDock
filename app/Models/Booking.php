<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    public const OCCUPANCY_DAYS = 365;

    public const RENEWAL_WINDOW_DAYS = 60;

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';

    public const PAYMENT_STATUSES = [
        self::PAYMENT_UNPAID => 'Unpaid',
        self::PAYMENT_PENDING => 'Pending',
        self::PAYMENT_PAID => 'Paid',
        self::PAYMENT_FAILED => 'Failed',
    ];

    public const GATEWAY_PAYSTACK = 'paystack';
    public const GATEWAY_MTN_MOMO = 'mtn_momo';
    public const GATEWAY_MOMO = 'momo';
    public const GATEWAY_BANK = 'bank';

    public const GATEWAYS = [
        self::GATEWAY_PAYSTACK => 'Paystack',
        self::GATEWAY_MTN_MOMO => 'MTN Mobile Money',
        self::GATEWAY_MOMO => 'Mobile Money',
        self::GATEWAY_BANK => 'Bank Transfer',
    ];

    protected $fillable = [
        'booking_ref',
        'user_id',
        'hostel_id',
        'room_id',
        'student_name',
        'email',
        'phone',
        'institution',
        'gender',
        'room_type',
        'academic_year',
        'amount',
        'status',
        'payment_status',
        'gateway',
        'paystack_reference',
        'paystack_transaction_ref',
        'momo_request_reference',
        'momo_phone',
        'provider_reference',
        'paid_at',
        'expires_at',
        'renewal_locked_at',
        'renewal_count',
        'renewal_window_notified_at',
        'renewal_paystack_reference',
        'renewal_paystack_transaction_ref',
        'renewal_momo_reference',
        'renewal_momo_transaction_ref',
        'renewal_phone',
    ];

    protected $casts = [
        'amount' => 'float',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'renewal_locked_at' => 'datetime',
        'renewal_window_notified_at' => 'datetime',
        'renewal_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function isPayable(): bool
    {
        return $this->payment_status !== self::PAYMENT_PAID
            && ! in_array($this->status, ['cancelled'], true);
    }

    public function markPaid(string $paystackTransactionRef = null): self
    {
        return $this->markPaidVia(self::GATEWAY_PAYSTACK, $paystackTransactionRef);
    }

    public function markPaidVia(string $gateway, string $transactionRef = null): self
    {
        $this->payment_status = self::PAYMENT_PAID;
        $this->gateway = $gateway;

        if ($gateway === self::GATEWAY_PAYSTACK) {
            $this->paystack_transaction_ref = $transactionRef ?? $this->paystack_transaction_ref;
        } else {
            $this->provider_reference = $transactionRef ?? $this->provider_reference;
        }

        $this->paid_at = now();
        $this->save();

        return $this;
    }

    public function markMomoPending(string $requestReference, string $phone): self
    {
        $this->gateway = self::GATEWAY_MTN_MOMO;
        $this->payment_status = self::PAYMENT_PENDING;
        $this->momo_request_reference = $requestReference;
        $this->momo_phone = $phone;
        $this->save();

        return $this;
    }

    public function isMomoPending(): bool
    {
        return $this->gateway === self::GATEWAY_MTN_MOMO
            && $this->payment_status === self::PAYMENT_PENDING;
    }

    public function gatewayLabel(): string
    {
        return self::GATEWAYS[$this->gateway] ?? ucfirst((string) $this->gateway);
    }

    public function markFailed(): self
    {
        $this->payment_status = self::PAYMENT_FAILED;
        $this->save();

        return $this;
    }

    public function paymentStatusLabel(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isOccupying($at = null): bool
    {
        $at = $at ?: now();

        return $this->isConfirmed() && ($this->expires_at === null || $this->expires_at->gt($at));
    }

    /**
     * Set the 365-day occupancy end once a booking becomes confirmed.
     */
    public function ensureOccupancyExpiry(): self
    {
        if ($this->isConfirmed() && $this->expires_at === null) {
            $this->expires_at = now()->addDays(self::OCCUPANCY_DAYS);
            $this->save();
        }

        return $this;
    }

    public function renewalOpensAt()
    {
        return $this->expires_at ? $this->expires_at->copy()->subDays(self::RENEWAL_WINDOW_DAYS) : null;
    }

    public function isWithinRenewalWindow($at = null): bool
    {
        if (! $this->isConfirmed() || ! $this->expires_at) {
            return false;
        }

        $at = $at ?: now();

        return $at->gte($this->renewalOpensAt()) && $at->lt($this->expires_at);
    }

    public function isRenewalLocked(): bool
    {
        return $this->renewal_locked_at !== null;
    }

    public function lockForRenewal(): self
    {
        $this->renewal_locked_at = now();
        $this->save();

        return $this;
    }

    public function renewalPrice(): float
    {
        return $this->room ? (float) $this->room->price : (float) $this->amount;
    }

    public function startRenewalPaystack(string $reference): self
    {
        $this->renewal_paystack_reference = $reference;
        $this->save();

        return $this;
    }

    public function startRenewalMomo(string $reference, string $phone): self
    {
        $this->renewal_momo_reference = $reference;
        $this->renewal_phone = $phone;
        $this->save();

        return $this;
    }

    public function isRenewalMomoPending(): bool
    {
        return $this->renewal_momo_reference !== null && $this->renewal_momo_transaction_ref === null;
    }

    public function clearRenewalMomoPending(): self
    {
        $this->renewal_momo_reference = null;
        $this->renewal_phone = null;
        $this->save();

        return $this;
    }

    /**
     * Extend the occupancy by another term once the renewal payment succeeds.
     *
     * @param  string  $transactionRef
     */
    public function applyRenewal(string $transactionRef, string $gateway): self
    {
        if ($gateway === self::GATEWAY_MTN_MOMO || $gateway === self::GATEWAY_MOMO) {
            $this->renewal_momo_transaction_ref = $transactionRef;
            $this->provider_reference = $transactionRef;
        } else {
            $this->renewal_paystack_transaction_ref = $transactionRef;
            $this->paystack_transaction_ref = $transactionRef;
        }

        $this->expires_at = now()->addDays(self::OCCUPANCY_DAYS);
        $this->renewal_locked_at = null;
        $this->renewal_window_notified_at = null;
        $this->renewal_count = (int) $this->renewal_count + 1;
        $this->paid_at = now();
        $this->save();

        return $this;
    }

    public static function paystackReference(): string
    {
        do {
            $ref = strtoupper('PSKB-' . Str::random(12));
        } while (static::where('paystack_reference', $ref)->exists());

        return $ref;
    }

    public static function renewalPaystackReference(): string
    {
        do {
            $ref = strtoupper('RENW-' . Str::random(12));
        } while (static::where('renewal_paystack_reference', $ref)->exists());

        return $ref;
    }
}