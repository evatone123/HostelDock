<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\MTNMoMoService;

class HostelPaymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'momo_provider',
        'momo_number',
        'momo_account_name',
        'momo_api_enabled',
        'momo_api_user',
        'momo_api_key',
        'momo_subscription_key',
        'momo_target_environment',
        'merchant_number',
        'bank_name',
        'bank_branch',
        'bank_account_name',
        'bank_account_number',
        'instructions',
        'is_active',
        'paystack_enabled',
        'paystack_public_key',
        'paystack_secret_key',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'paystack_enabled' => 'boolean',
        'paystack_secret_key' => 'encrypted',
        'momo_api_enabled' => 'boolean',
        'momo_api_key' => 'encrypted',
        'momo_subscription_key' => 'encrypted',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function hasBank(): bool
    {
        return (bool) ($this->bank_name && $this->bank_account_number);
    }

    public function hasMomo(): bool
    {
        return (bool) ($this->momo_number && $this->momo_provider);
    }

    public function hasPaystack(): bool
    {
        return $this->paystack_enabled
            && (bool) $this->paystack_secret_key
            && (bool) $this->paystack_public_key;
    }

    public function hasMtnMoMoApi(): bool
    {
        return $this->momo_api_enabled
            && (bool) $this->momo_api_user
            && (bool) $this->momo_api_key
            && (bool) $this->momo_subscription_key;
    }

    public function momoApiCredentials(): array
    {
        return [
            'api_user' => $this->momo_api_user ?? '',
            'api_key' => $this->momo_api_key ?? '',
            'subscription_key' => $this->momo_subscription_key ?? '',
            'target' => $this->momo_target_environment ?: MTNMoMoService::TARGET_SANDBOX,
        ];
    }
}
