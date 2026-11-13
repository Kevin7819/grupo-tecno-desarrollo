<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Application\Cms\ActivityEntry;
use App\Application\Cms\AdminActivityLogger;
use App\Application\Cms\AdminActivityRepository;
use App\Application\Cms\AdminActor;
use App\Models\AdminActivity;
use DateTimeImmutable;

final class EloquentAdminActivityRepository implements AdminActivityLogger, AdminActivityRepository
{
    public function record(AdminActor $actor, string $action, string $subject): void
    {
        AdminActivity::query()->create([
            'admin_user_id' => $actor->id,
            'actor_name' => $actor->name,
            'action' => $action,
            'subject' => mb_substr($subject, 0, 160),
        ]);
    }

    public function latest(int $limit): array
    {
        return AdminActivity::query()
            ->latest('id')
            ->limit($limit)
            ->get()
            ->map(static fn (AdminActivity $row): ActivityEntry => new ActivityEntry(
                action: $row->action,
                subject: $row->subject,
                actorName: $row->actor_name,
                happenedAt: new DateTimeImmutable((string) $row->created_at),
            ))
            ->all();
    }
}
