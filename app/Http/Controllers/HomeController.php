<?php

namespace App\Http\Controllers;

use App\Application\Cms\GalleryRepository;
use App\Application\Cms\ProgramRepository;
use App\Application\Cms\SiteContentRepository;
use App\Application\Cms\StationSettingsProvider;
use App\Application\Radio\ContactChannelsProvider;
use App\Application\Radio\StreamConfigProvider;
use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __invoke(
        StreamConfigProvider $streams,
        ContactChannelsProvider $channels,
        StationSettingsProvider $settings,
        ProgramRepository $programs,
        SiteContentRepository $content,
        GalleryRepository $gallery,
    ): View {
        $station = $settings->current();

        return view('home', [
            'station' => $station,
            'stream' => $streams->current(),
            'channels' => $channels->current(),
            'programs' => $programs->published(),
            'content' => $content->current(),
            'gallery' => $gallery->published(),
        ]);
    }
}
