<?php

namespace App\Http\Controllers\Admin;

use App\Application\Cms\AdminActivityRepository;
use App\Application\Cms\HostRepository;
use App\Application\Cms\ProgramRepository;
use App\Application\Cms\StationSettingsProvider;
use App\Application\Cms\UpcomingPrograms;
use App\Domain\Cms\ProgramStatus;
use App\Domain\Cms\Weekday;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class DashboardController extends Controller
{
    public function __invoke(
        StationSettingsProvider $settings,
        ProgramRepository $programs,
        HostRepository $hosts,
        AdminActivityRepository $activity,
        UpcomingPrograms $upcoming,
    ): View {
        $station = $settings->current();

        return view('admin.dashboard', [
            'station' => $station,
            'publishedCount' => $programs->countByStatus(ProgramStatus::Published),
            'draftCount' => $programs->countByStatus(ProgramStatus::Draft),
            'inactiveCount' => $programs->countByStatus(ProgramStatus::Inactive),
            'hostCount' => $hosts->countActive(),
            'upcoming' => $upcoming->handle(
                $programs->all(),
                Weekday::fromNumber((int) now()->isoWeekday()),
                now()->format('H:i'),
                4,
            ),
            'activities' => $activity->latest(8),
            'storageLinked' => file_exists(public_path('storage')),
        ]);
    }
}
