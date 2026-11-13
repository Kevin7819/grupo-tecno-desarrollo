<?php

namespace App\Infrastructure\Cms;

use App\Application\Cms\ContactRepository;
use App\Application\Radio\ContactChannelsProvider;
use App\Domain\Radio\ContactChannels;
use App\Infrastructure\Radio\ConfigContactChannelsProvider;

/**
 * Public contact channels with the same precedence used by the station
 * settings:
 *   1. a valid value stored in the database;
 *   2. the RADIO_CONTACT_* / RADIO_SOCIAL_* environment configuration;
 *   3. the channel is hidden.
 *
 * Invalid stored values are filtered out, so a bad row never publishes a
 * broken link or hides a valid environment channel.
 */
final class ConfiguredContactChannelsProvider implements ContactChannelsProvider
{
    public function __construct(
        private readonly ContactRepository $contact,
        private readonly ConfigContactChannelsProvider $environment,
    ) {}

    public function current(): ContactChannels
    {
        $stored = $this->contact->current();
        $environment = $this->environment->current();

        $social = [];

        foreach ($this->contact->activeSocialLinks() as $link) {
            if ($link->isPublic()) {
                $social[$link->label] = $link->url;
            }
        }

        return ContactChannels::fromValues(
            email: $stored->email ?? $environment->email,
            social: $social !== [] ? $social : $environment->social,
            phone: $stored->phone,
        );
    }
}
