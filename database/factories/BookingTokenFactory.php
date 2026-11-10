<?php

namespace Database\Factories;

use App\Models\BookingToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingTokenFactory extends Factory
{
    protected $model = BookingToken::class;

    public function definition()
    {
        $tokenRef = BookingToken::generateReference();

        return [
            'token_ref' => $tokenRef,
            'user_id' => User::factory(),
            'amount' => BookingToken::PRICE_GHS,
            'status' => BookingToken::STATUS_PENDING,
            'gateway' => 'paystack',
            'paystack_reference' => BookingToken::paystackReference(),
        ];
    }

    public function active()
    {
        return $this->state(fn () => ['status' => BookingToken::STATUS_ACTIVE]);
    }

    public function failed()
    {
        return $this->state(fn () => ['status' => BookingToken::STATUS_FAILED]);
    }

    public function pending()
    {
        return $this->state(fn () => ['status' => BookingToken::STATUS_PENDING]);
    }
}
