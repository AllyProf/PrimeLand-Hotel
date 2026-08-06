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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'openweather' => [
        'api_key' => env('OPENWEATHER_API_KEY'),
    ],

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'sms' => [
        'token'     => env('SMS_TOKEN'),
        'sender_id' => env('SMS_SENDER_ID', 'PrimeLand'),
        'base_url'  => env('SMS_BASE_URL', 'https://messaging-service.co.tz'),
    ],

    'whatsapp' => [
        'token'     => env('WHATSAPP_TOKEN', env('SMS_TOKEN')),
        'sender_id' => env('WHATSAPP_SENDER_ID', env('SMS_SENDER_ID', 'PrimeLand')),
        'base_url'  => env('WHATSAPP_BASE_URL', 'https://messaging-service.co.tz'),
    ],

    'aiosell' => [
        'booking_url' => env('AIOSELL_BOOKING_URL', 'https://be.aiosell.com/book/d90c1b0297'),
    ],

];
