<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'whatsapp' => [
        'enabled' => env('OPENWA_ENABLED', false),
        'base_url' => rtrim(env('OPENWA_BASE_URL', 'http://127.0.0.1:2785/api'), '/'),
        'api_key' => env('OPENWA_API_KEY'),
        'session_name' => env('OPENWA_SESSION_NAME', 'main'),
        'admin_alert_number' => env('OPENWA_ADMIN_ALERT_NUMBER'),
    ],

    'whatsapp_business' => [
        'enabled' => env('WHATSAPP_CLOUD_ENABLED', false),
        'graph_version' => env('WHATSAPP_CLOUD_GRAPH_VERSION', 'v21.0'),
        'access_token' => env('WHATSAPP_CLOUD_ACCESS_TOKEN'),
        'phone_number_id' => env('WHATSAPP_CLOUD_PHONE_NUMBER_ID'),
        'waba_id' => env('WHATSAPP_CLOUD_WABA_ID'),
        'app_id' => env('WHATSAPP_CLOUD_APP_ID'),
        'app_secret' => env('WHATSAPP_CLOUD_APP_SECRET'),
        'verify_token' => env('WHATSAPP_CLOUD_VERIFY_TOKEN'),
        'activation_status' => env('WHATSAPP_CLOUD_ACTIVATION_STATUS', 'pending'),
        'admin_alert_number' => env('WHATSAPP_CLOUD_ADMIN_ALERT_NUMBER', env('OPENWA_ADMIN_ALERT_NUMBER')),
    ],

    'recaptcha' => [
        'enabled' => env('RECAPTCHA_ENABLED', false),
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],

];
