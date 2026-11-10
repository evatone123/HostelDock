<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paystack Credentials
    |--------------------------------------------------------------------------
    |
    | Set PAYSTACK_PUBLIC_KEY and PAYSTACK_SECRET_KEY in your .env file.
    | Use the sandbox (test) keys for development; live keys for production.
    |
    */

    'public_key' => env('PAYSTACK_PUBLIC_KEY', ''),

    'secret_key' => env('PAYSTACK_SECRET_KEY', ''),

    'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),

    'currency' => env('PAYSTACK_CURRENCY', 'GHS'),

    /*
    |--------------------------------------------------------------------------
    | Booking Access Token Fee
    |--------------------------------------------------------------------------
    |
    | The one-time fee (in GHS) a student pays for access to the booking page.
    |
    */

    'token_fee' => env('BOOKING_TOKEN_FEE', 70.00),
];