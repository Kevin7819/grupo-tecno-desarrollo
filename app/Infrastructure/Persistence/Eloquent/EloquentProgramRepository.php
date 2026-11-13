<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Application\Cms\ProgramData;
use App\Application\Cms\ProgramRepository;
use App\Domain\Cms\Host as HostValue;
use App\Domain\Cms\Program as ProgramValue;
use App\Domain\Cms\ProgramStatus;
use App\Domain\Cms\ScheduleSlot;
use App\Models\Host as HostModel;
use App\Models\Program as ProgramModel;
use App\Models\ProgramSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class EloquentProgramRepository implements ProgramRepository
{
    public function all(): array
    {
        return $this->ordered()
            ->get()
            ->map($this->toDomain(...))
            ->all();
    }

    public function published(): array
    {
        return $this->ordered()
            ->where('status', ProgramStatus::Published->value)
            ->get()
            ->map($this->toDomain(...))
            ->all();
    }

    public function find(int $id): ?ProgramValue
    {
        $model = $this->withRelations()->find($id);

        return $model !== null ? $this->toDomain($model) : null;
    }

    public function create(ProgramData $data): ProgramValue
    {
        $model = DB::transaction(function () use ($data): ProgramModel {
            $model = ProgramModel::query()->create($this->attributes($data));
            $model->hosts()->sync($data->hostIds);
            $this->writeSchedule($model, $data->schedule);

            return $model;
        });

        return $this->toDomain($this->withRelations()->findOrFail($model->id));
    }

    public function update(ProgramValue $program, ProgramData $data): ProgramValue
    {
        $model = ProgramModel::query()->findOrFail($program->id);

        DB::transaction(function () use ($model, $data): void {
            $model->update($this->attributes($data));
            $model->hosts()->sync($data->hostIds);
            $model->schedules()->delete();
            $this->writeSchedule($model, $data->schedule);
        });

        return $this->toDomain($this->withRelations()->findOrFail($model->id));
    }

    public function updateStatus(ProgramValue $program, ProgramStatus $status): ProgramValue
    {
        $model = ProgramModel::query()->findOrFail($program->id);
        $model->update(['status' => $status->value]);

        return $this->toDomain($this->withRelations()->findOrFail($model->id));
    }

    public function delete(ProgramValue $program): void
    {
        ProgramModel::query()->findOrFail($program->id)->delete();
    }

    public function countByStatus(ProgramStatus $status): int
    {
        return ProgramModel::query()->where('status', $status->value)->count();
    }

    /**
     * @return Builder<ProgramModel>
     */
    private function ordered(): Builder
    {
        return $this->withRelations()
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * @return Builder<ProgramModel>
     */
    private function withRelations(): Builder
    {
        return ProgramModel::query()->with(['hosts', 'schedules']);
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(ProgramData $data): array
    {
        return [
            'name' => $data->name,
            'description' => $data->description,
            'cover_path' => $data->coverPath,
            'status' => $data->status->value,
            'sort_order' => $data->sortOrder,
        ];
    }

    /**
     * @param  ScheduleSlot[]  $schedule
     */
    private function writeSchedule(ProgramModel $model, array $schedule): void
    {
        $model->schedules()->createMany(array_map(static fn (ScheduleSlot $slot): array => [
            'weekday' => $slot->weekday->value,
            'starts_at' => $slot->startsAt,
            'ends_at' => $slot->endsAt,
        ], $schedule));
    }

    private function toDomain(ProgramModel $model): ProgramValue
    {
        /** @var Collection<int, ProgramSchedule> $schedules */
        $schedules = $model->schedules;

        return ProgramValue::fromValues(
            id: $model->id,
            name: $model->name,
            description: $model->description,
            coverPath: $model->cover_path,
            status: ProgramStatus::fromString($model->status),
            sortOrder: (int) $model->sort_order,
            schedule: $schedules->map(static fn (ProgramSchedule $row): ScheduleSlot => ScheduleSlot::fromValues(
                $row->weekday,
                substr((string) $row->starts_at, 0, 5),
                substr((string) $row->ends_at, 0, 5),
            ))->all(),
            hosts: $model->hosts->map(static fn (HostModel $host): HostValue => HostValue::fromValues(
                id: $host->id,
                name: $host->name,
                bio: $host->bio,
                photoPath: $host->photo_path,
                isActive: (bool) $host->is_active,
            ))->all(),
        );
    }
}
