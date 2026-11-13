<?php

namespace App\Application\Cms;

use App\Domain\Cms\Program;
use App\Domain\Cms\Weekday;

/**
 * Calculates the next broadcasts from the weekly programme.
 *
 * The calculation is pure: the caller provides the current weekday and time,
 * so the result is deterministic and testable without touching the clock.
 */
final class UpcomingPrograms
{
    /**
     * @param  Program[]  $programs
     * @return ScheduleOccurrence[]
     */
    public function handle(array $programs, Weekday $today, string $time, int $limit = 3): array
    {
        $now = self::minutes($time);
        $occurrences = [];

        foreach ($programs as $program) {
            if (! $program->isPubliclyVisible()) {
                continue;
            }

            foreach ($program->schedule as $slot) {
                $daysAhead = $today->daysUntil($slot->weekday);
                $isOnAir = false;

                if ($daysAhead === 0) {
                    if ($slot->endsAtMinutes() <= $now) {
                        $daysAhead = 7;
                    } else {
                        $isOnAir = $slot->startsAtMinutes() <= $now;
                    }
                }

                $occurrences[] = new ScheduleOccurrence($program, $slot, $daysAhead, $isOnAir);
            }
        }

        usort($occurrences, static fn (ScheduleOccurrence $a, ScheduleOccurrence $b): int => [$a->daysAhead, $a->slot->startsAt] <=> [$b->daysAhead, $b->slot->startsAt]);

        return $limit > 0 ? array_slice($occurrences, 0, $limit) : $occurrences;
    }

    private static function minutes(string $time): int
    {
        $parts = explode(':', trim($time));

        return ((int) ($parts[0] ?? 0)) * 60 + ((int) ($parts[1] ?? 0));
    }
}
