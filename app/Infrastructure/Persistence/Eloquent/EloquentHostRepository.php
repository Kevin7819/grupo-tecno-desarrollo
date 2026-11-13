<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Application\Cms\HostInput;
use App\Application\Cms\HostRepository;
use App\Domain\Cms\Host as HostValue;
use App\Models\Host as HostModel;

final class EloquentHostRepository implements HostRepository
{
    public function all(): array
    {
        return HostModel::query()
            ->orderBy('name')
            ->get()
            ->map($this->toDomain(...))
            ->all();
    }

    public function active(): array
    {
        return HostModel::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map($this->toDomain(...))
            ->all();
    }

    public function find(int $id): ?HostValue
    {
        $model = HostModel::query()->find($id);

        return $model !== null ? $this->toDomain($model) : null;
    }

    public function create(HostInput $input): HostValue
    {
        return $this->toDomain(HostModel::query()->create($this->attributes($input)));
    }

    public function update(HostValue $host, HostInput $input): HostValue
    {
        $model = HostModel::query()->findOrFail($host->id);
        $model->update($this->attributes($input));

        return $this->toDomain($model);
    }

    public function delete(HostValue $host): void
    {
        HostModel::query()->findOrFail($host->id)->delete();
    }

    public function countActive(): int
    {
        return HostModel::query()->where('is_active', true)->count();
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(HostInput $input): array
    {
        return [
            'name' => $input->name,
            'bio' => $input->bio,
            'photo_path' => $input->photoPath,
            'is_active' => $input->isActive,
        ];
    }

    private function toDomain(HostModel $model): HostValue
    {
        return HostValue::fromValues(
            id: $model->id,
            name: $model->name,
            bio: $model->bio,
            photoPath: $model->photo_path,
            isActive: (bool) $model->is_active,
        );
    }
}
