<?php

namespace App\Infrastructure\Radio;

use App\Application\Radio\StreamConfigProvider;
use App\Domain\Radio\StreamConfig;

final class ConfigStreamConfigProvider implements StreamConfigProvider
{
    public function current(): StreamConfig
    {
        return StreamConfig::fromValues(
            url: config('radio.stream.url'),
            format: config('radio.stream.format'),
            name: config('radio.stream.name'),
            allowHttp: app()->environment('local'),
        );
    }
}
