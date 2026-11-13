<?php

namespace App\Application\Cms;

use App\Domain\Cms\SocialLink;

final class DeleteSocialLink
{
    public function __construct(
        private readonly ContactRepository $contact,
        private readonly AdminActivityLogger $activity,
    ) {}

    public function handle(SocialLink $link, AdminActor $actor): void
    {
        $this->contact->deleteSocialLink($link);
        $this->activity->record($actor, 'social.deleted', $link->label);
    }
}
