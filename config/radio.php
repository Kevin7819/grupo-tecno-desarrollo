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
    | Supported formats: mp3, aac, hls.
    |
    */

    'stream' => [
        'url' => env('RADIO_STREAM_URL'),
        'format' => env('RADIO_STREAM_FORMAT', 'mp3'),
        'name' => env('RADIO_STREAM_NAME'),
    ],

];
