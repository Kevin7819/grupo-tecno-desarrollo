<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Radio stream
    |--------------------------------------------------------------------------
    |
    | The audio is served by an external streaming provider. Laravel only
    | renders the player. Leave RADIO_STREAM_URL empty to show the
    | "stream not configured" state instead of a broken control.
    |
    | Production requires an https:// URL. http:// is accepted only in the
    | local environment for development streams.
    |
    | Supported formats: mp3, aac, hls.
    |
    */

    'stream' => [
        'url' => env('RADIO_STREAM_URL'),
        'format' => env('RADIO_STREAM_FORMAT', 'mp3'),
        'name' => env('RADIO_STREAM_NAME'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Public contact channels
    |--------------------------------------------------------------------------
    |
    | Only https:// links and a valid e-mail address are published. Empty
    | values hide the channel instead of rendering a control that does
    | nothing. See README.md for the available variables.
    |
    */

    'contact' => [
        'email' => env('RADIO_CONTACT_EMAIL'),
    ],

    'social' => [
        'Instagram' => env('RADIO_SOCIAL_INSTAGRAM'),
        'Facebook' => env('RADIO_SOCIAL_FACEBOOK'),
        'TikTok' => env('RADIO_SOCIAL_TIKTOK'),
        'YouTube' => env('RADIO_SOCIAL_YOUTUBE'),
        'WhatsApp' => env('RADIO_SOCIAL_WHATSAPP'),
    ],

];
