<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MTN MoMo API
    |--------------------------------------------------------------------------
    |
    | Base URLs for the MTN MoMo (Collection / Request to Pay) API.
    | Per-hostel credentials (API user, API key, product subscription key,
    | target environment) are configured in the admin panel and stored in
    | the `hostel_payment_details` table.
    |
    */

    'sandbox_base_url' => env('MTN_MOMO_SANDBOX_BASE_URL', 'https://sandbox.momodeveloper.mtn.com'),

    'live_base_url' => env('MTN_MOMO_LIVE_BASE_URL', 'https://momodeveloper.mtn.com'),

    'currency' => env('MTN_MOMO_CURRENCY', 'GHS'),
];