<?php

namespace App\Application\Cms;

use App\Domain\Cms\GalleryImage;

final class SaveGalleryImage
{
    public function __construct(
        private readonly GalleryRepository $images,
        private readonly AdminActivityLogger $activity,
    ) {}

    public function handle(?GalleryImage $existing, GalleryImageInput $input, AdminActor $actor): GalleryImage
    {
        if ($existing === null) {
            $image = $this->images->create($input);
            $this->activity->record($actor, 'gallery.created', $image->title);

            return $image;
        }

        $image = $this->images->update($existing, $input);
        $this->activity->record($actor, 'gallery.updated', $image->title);

        return $image;
    }
}
