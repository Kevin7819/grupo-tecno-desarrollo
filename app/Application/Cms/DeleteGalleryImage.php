<?php

namespace App\Application\Cms;

use App\Domain\Cms\GalleryImage;

final class DeleteGalleryImage
{
    public function __construct(
        private readonly GalleryRepository $images,
        private readonly AdminActivityLogger $activity,
    ) {}

    public function handle(GalleryImage $image, AdminActor $actor): void
    {
        $this->images->delete($image);
        $this->activity->record($actor, 'gallery.deleted', $image->title);
    }
}
