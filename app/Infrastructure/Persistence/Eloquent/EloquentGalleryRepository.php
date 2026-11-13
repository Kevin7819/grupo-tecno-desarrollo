<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Application\Cms\GalleryImageInput;
use App\Application\Cms\GalleryRepository;
use App\Domain\Cms\GalleryImage as GalleryImageValue;
use App\Models\GalleryImage as GalleryImageModel;

final class EloquentGalleryRepository implements GalleryRepository
{
    public function all(): array
    {
        return GalleryImageModel::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map($this->toDomain(...))
            ->all();
    }

    public function published(): array
    {
        return GalleryImageModel::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map($this->toDomain(...))
            ->all();
    }

    public function find(int $id): ?GalleryImageValue
    {
        $model = GalleryImageModel::query()->find($id);

        return $model !== null ? $this->toDomain($model) : null;
    }

    public function create(GalleryImageInput $input): GalleryImageValue
    {
        return $this->toDomain(GalleryImageModel::query()->create($this->attributes($input)));
    }

    public function update(GalleryImageValue $image, GalleryImageInput $input): GalleryImageValue
    {
        $model = GalleryImageModel::query()->findOrFail($image->id);
        $model->update($this->attributes($input));

        return $this->toDomain($model);
    }

    public function delete(GalleryImageValue $image): void
    {
        GalleryImageModel::query()->findOrFail($image->id)->delete();
    }

    public function countPublished(): int
    {
        return GalleryImageModel::query()->where('is_published', true)->count();
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(GalleryImageInput $input): array
    {
        return [
            'title' => $input->title,
            'image_path' => $input->imagePath,
            'alt_text' => $input->altText,
            'caption' => $input->caption,
            'sort_order' => $input->sortOrder,
            'is_published' => $input->isPublished,
        ];
    }

    private function toDomain(GalleryImageModel $model): GalleryImageValue
    {
        return GalleryImageValue::fromValues(
            id: $model->id,
            title: $model->title,
            imagePath: $model->image_path,
            altText: $model->alt_text,
            caption: $model->caption,
            sortOrder: (int) $model->sort_order,
            isPublished: (bool) $model->is_published,
        );
    }
}
