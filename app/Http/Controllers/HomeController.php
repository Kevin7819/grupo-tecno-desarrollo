<?php

namespace App\Http\Controllers;

use App\Application\Radio\StreamConfigProvider;
use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __invoke(StreamConfigProvider $streams): View
    {
        return view('home', [
            'stream' => $streams->current(),
        ]);
    }
}
