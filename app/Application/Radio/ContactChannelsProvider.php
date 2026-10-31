<?php

namespace App\Application\Radio;

use App\Domain\Radio\ContactChannels;

interface ContactChannelsProvider
{
    public function current(): ContactChannels;
}
