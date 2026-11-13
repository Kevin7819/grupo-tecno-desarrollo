<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Application\Cms\SiteContentRepository;
use App\Domain\Cms\SiteContent;
use App\Models\SiteContent as SiteContentModel;
use Illuminate\Database\QueryException;

final class EloquentSiteContentRepository implements SiteContentRepository
{
    private const CONTENT_ID = 1;

    public function current(): SiteContent
    {
        try {
            $row = SiteContentModel::query()->orderBy('id')->first();
        } catch (QueryException) {
            // The table may not exist yet on a fresh clone. The public page
            // must keep its current copy instead of failing.
            return SiteContent::defaults();
        }

        if ($row === null) {
            return SiteContent::defaults();
        }

        return SiteContent::fromValues(
            heroKicker: $row->hero_kicker,
            heroTitle: $row->hero_title,
            heroIntro: $row->hero_intro,
            aboutTitle: $row->about_title,
            aboutText: $row->about_text,
            galleryTitle: $row->gallery_title,
            galleryIntro: $row->gallery_intro,
            contactTitle: $row->contact_title,
            contactIntro: $row->contact_intro,
        );
    }

    public function save(SiteContent $content): SiteContent
    {
        SiteContentModel::query()->updateOrCreate(
            ['id' => self::CONTENT_ID],
            [
                'hero_kicker' => $content->heroKicker,
                'hero_title' => $content->heroTitle,
                'hero_intro' => $content->heroIntro,
                'about_title' => $content->aboutTitle,
                'about_text' => $content->aboutText,
                'gallery_title' => $content->galleryTitle,
                'gallery_intro' => $content->galleryIntro,
                'contact_title' => $content->contactTitle,
                'contact_intro' => $content->contactIntro,
            ],
        );

        return $content;
    }
}
