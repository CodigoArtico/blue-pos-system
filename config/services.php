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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],
    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI'),
    ],

    'apis' => [
        'token' => env('APIS_TOKEN'),
        'url_dni' => env('APIS_URL_DNI', 'https://api-codart.cgrt.org/api/v1/consultas/reniec/dni/'),
        'url_ruc' => env('APIS_URL_RUC', 'https://api-codart.cgrt.org/api/v1/consultas/sunat/ruc/'),
    ],

    'telegram' => [
        'url' => env('TELEGRAM_URL', 'https://t.me/codigoartico'),
    ],

    'social' => [
        'instagram' => env('INSTAGRAM_URL', 'https://www.instagram.com/codigo_artico/'),
        'youtube' => env('YOUTUBE_URL', 'https://youtube.com/@codigoartico?si=Ka3_NzcINLMgl_KI'),
        'github' => env('GITHUB_URL', 'https://github.com/CodigoArtico?tab=repositories'),
        'tiktok' => env('TIKTOK_URL', 'https://www.tiktok.com/@codigoartico'),
    ],

];