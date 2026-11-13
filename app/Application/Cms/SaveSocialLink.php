<?php

namespace App\Application\Cms;

use App\Domain\Cms\SocialLink;

final class SaveSocialLink
{
    public function __construct(
        private readonly ContactRepository $contact,
        private readonly AdminActivityLogger $activity,
    ) {}

    public function handle(?SocialLink $existing, SocialLinkInput $input, AdminActor $actor): SocialLink
    {
        if ($existing === null) {
            $link = $this->contact->createSocialLink($input);
            $this->activity->record($actor, 'social.created', $link->label);

            return $link;
        }

        $link = $this->contact->updateSocialLink($existing, $input);
        $this->activity->record($actor, 'social.updated', $link->label);

        return $link;
    }
}
