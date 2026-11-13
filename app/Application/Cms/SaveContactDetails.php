<?php

namespace App\Application\Cms;

use App\Domain\Cms\ContactDetails;

final class SaveContactDetails
{
    public function __construct(
        private readonly ContactRepository $contact,
        private readonly AdminActivityLogger $activity,
    ) {}

    public function handle(ContactDetails $details, AdminActor $actor): ContactDetails
    {
        $saved = $this->contact->save($details);

        $this->activity->record($actor, 'contact.updated', 'Canales de contacto');

        return $saved;
    }
}
