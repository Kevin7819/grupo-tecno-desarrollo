<?php

namespace App\Application\Radio;

use App\Domain\Radio\StreamConfig;

interface StreamConfigProvider
{
    public function current(): StreamConfig;
}
