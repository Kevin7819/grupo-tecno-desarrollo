<?php

namespace App\Application\Cms;

use App\Domain\Cms\ContactDetails;
use App\Domain\Cms\SocialLink;

interface ContactRepository
{
    /**
     * Stored contact details, or an empty value when nothing was saved yet.
     */
    public function current(): ContactDetails;

    public function save(ContactDetails $details): ContactDetails;

    /**
     * @return SocialLink[]
     */
    public function socialLinks(): array;

    /**
     * @return SocialLink[]
     */
    public function activeSocialLinks(): array;

    public function findSocialLink(int $id): ?SocialLink;

    public function createSocialLink(SocialLinkInput $input): SocialLink;

    public function updateSocialLink(SocialLink $link, SocialLinkInput $input): SocialLink;

    public function deleteSocialLink(SocialLink $link): void;
}
