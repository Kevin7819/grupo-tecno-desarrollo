<?php

namespace App\Application\Cms;

use App\Domain\Cms\SiteContent;

interface SiteContentRepository
{
    /**
     * The stored copy, or the project defaults when nothing has been saved.
     */
    public function current(): SiteContent;

    public function save(SiteContent $content): SiteContent;
}
