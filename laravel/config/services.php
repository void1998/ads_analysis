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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'tiktok' => [
        'access_token' => env('TIKTOK_ACCESS_TOKEN'),
        'advertiser_id' => env('TIKTOK_ADVERTISER_ID')

    ],
    'snapchat' => [
        'refresh_token' => 'eyJraWQiOiJyZWZyZXNoLXRva2VuLWExMjhnY20uMCIsInR5cCI6IkpXVCIsImVuYyI6IkExMjhHQ00iLCJhbGciOiJkaXIifQ..uaCKF9wt5HIe1FjA.FAYcibKTQTC9p1Eml7Z567Iev9V18AyLjratdiE1eSkbQwNDakyi776aaOE4r0JN_IMzSksLTTYd0cagPDCHuauFZdOrZ4dlT2lZYgnHzRLNGjQ91URm4_Tr-9nZA4881pADo1up2_9iN8t8pQ_B2aoKaLhVDICfMKWCAsczoYDNQnYEMoXSN5NBLbUX57ut472liTscEn2MgyDVsu8Y2yjgVxHqfa4VvqN8TmM26Yv7hkdBaTPg-Zamu4kErfXpJ1tFMoKT4CTsCBo.95TNLnPu9-MxdiYPMZc0HA',
        'client_id' => 'c98e28be-3a15-44be-b0c5-7ff9445d482d',
        'client_secret' => '7d54b6641f3ad17b6c12'
    ],
    'meta' => [
        'access_token' => 'EAAFLZAZC5BVxUBO7ZALlo4soEjatYAqjeiolsLBJJSYtmSqdBo0HNGPoA1Twax3O5FOYYlfX6ajkHxy3g2zjfiiN44voIEve4t6CheKUpKW4IeiXLEUrsHy6SKtwLdZAugei50ZA3Duso1BhMZCqsKJfc4C9S20CCM2NlOtupK9aisZAVPoID5MdzRm9MboHmOwszGg6rQUVGYf8FZB2'
    ]

];
