<?php

namespace App\Application\Cms;

use App\Domain\Cms\GalleryImage;

interface GalleryRepository
{
    /**
     * @return GalleryImage[]
     */
    public function all(): array;

    /**
     * Published images, already ordered for the public gallery.
     *
     * @return GalleryImage[]
     */
    public function published(): array;

    public function find(int $id): ?GalleryImage;

    public function create(GalleryImageInput $input): GalleryImage;

    public function update(GalleryImage $image, GalleryImageInput $input): GalleryImage;

    public function delete(GalleryImage $image): void;

    public function countPublished(): int;
}
