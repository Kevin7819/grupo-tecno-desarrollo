<?php

namespace App\Http\Controllers\Admin;

use App\Application\Cms\AdminActor;
use App\Application\Cms\SaveStationSettings;
use App\Application\Cms\StationSettingsInput;
use App\Application\Cms\StationSettingsProvider;
use App\Domain\Cms\StationSettings;
use App\Domain\Cms\StationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStationSettingsRequest;
use App\Infrastructure\Storage\PublicImageStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class StationSettingsController extends Controller
{
    public function edit(StationSettingsProvider $settings): View
    {
        return view('admin.settings.edit', [
            'settings' => $settings->current(),
        ]);
    }

    public function update(
        UpdateStationSettingsRequest $request,
        StationSettingsProvider $provider,
        SaveStationSettings $save,
        PublicImageStorage $images,
    ): RedirectResponse {
        $current = $provider->current();
        $logoPath = $this->logoPath($request, $current, $images);

        $input = new StationSettingsInput(
            stationName: $request->validated('station_name'),
            slogan: $request->validated('slogan'),
            description: $request->validated('description'),
            logoPath: $logoPath,
            streamUrl: $request->validated('stream_url'),
            streamFormat: (string) $request->validated('stream_format'),
            streamName: $request->validated('stream_name'),
            status: StationStatus::fromString($request->validated('status')),
        );

        $save->handle(
            $input,
            AdminActor::fromAdmin($request->user('admin')),
            allowHttp: app()->environment('local'),
        );

        $images->replace($current->logoPath, $logoPath);

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Configuración de la emisora guardada.');
    }

    private function logoPath(
        UpdateStationSettingsRequest $request,
        StationSettings $current,
        PublicImageStorage $images,
    ): ?string {
        if ($request->hasFile('logo')) {
            return $images->store($request->file('logo'), 'station');
        }

        if ($request->boolean('remove_logo')) {
            return null;
        }

        return $current->logoPath;
    }
}
