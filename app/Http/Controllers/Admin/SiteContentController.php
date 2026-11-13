<?php

namespace App\Http\Controllers\Admin;

use App\Application\Cms\AdminActor;
use App\Application\Cms\SaveSiteContent;
use App\Application\Cms\SiteContentRepository;
use App\Domain\Cms\SiteContent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSiteContentRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class SiteContentController extends Controller
{
    public function edit(SiteContentRepository $content): View
    {
        return view('admin.content.edit', [
            'content' => $content->current(),
        ]);
    }

    public function update(
        UpdateSiteContentRequest $request,
        SaveSiteContent $save,
    ): RedirectResponse {
        $content = SiteContent::fromValues(
            heroKicker: $request->validated('hero_kicker'),
            heroTitle: $request->validated('hero_title'),
            heroIntro: $request->validated('hero_intro'),
            aboutTitle: $request->validated('about_title'),
            aboutText: $request->validated('about_text'),
            galleryTitle: $request->validated('gallery_title'),
            galleryIntro: $request->validated('gallery_intro'),
            contactTitle: $request->validated('contact_title'),
            contactIntro: $request->validated('contact_intro'),
        );

        $save->handle($content, AdminActor::fromAdmin($request->user('admin')));

        return redirect()
            ->route('admin.content.edit')
            ->with('status', 'Contenido de la página pública guardado.');
    }
}
