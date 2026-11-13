<?php

namespace App\Application\Cms;

use App\Domain\Cms\SiteContent;

final class SaveSiteContent
{
    public function __construct(
        private readonly SiteContentRepository $content,
        private readonly AdminActivityLogger $activity,
    ) {}

    public function handle(SiteContent $content, AdminActor $actor): SiteContent
    {
        $saved = $this->content->save($content);

        $this->activity->record($actor, 'content.updated', 'Contenido de la página pública');

        return $saved;
    }
}
