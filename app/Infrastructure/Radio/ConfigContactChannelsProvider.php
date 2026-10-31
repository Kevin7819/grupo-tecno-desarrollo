<?php

namespace App\Infrastructure\Radio;

use App\Application\Radio\ContactChannelsProvider;
use App\Domain\Radio\ContactChannels;

final class ConfigContactChannelsProvider implements ContactChannelsProvider
{
    public function current(): ContactChannels
    {
        $social = config('radio.social', []);

        return ContactChannels::fromValues(
            email: config('radio.contact.email'),
            social: is_array($social) ? $social : [],
        );
    }
}
