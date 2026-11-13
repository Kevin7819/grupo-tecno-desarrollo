<?php

namespace App\Http\Controllers\Admin;

use App\Application\Cms\AdminActor;
use App\Application\Cms\ContactRepository;
use App\Application\Cms\DeleteSocialLink;
use App\Application\Cms\SaveContactDetails;
use App\Application\Cms\SaveSocialLink;
use App\Application\Cms\SocialLinkInput;
use App\Domain\Cms\ContactDetails;
use App\Domain\Cms\SocialLink;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SocialLinkRequest;
use App\Http\Requests\Admin\UpdateContactSettingsRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class ContactController extends Controller
{
    public function edit(ContactRepository $contact): View
    {
        return view('admin.contact.edit', [
            'details' => $contact->current(),
            'links' => $contact->socialLinks(),
        ]);
    }

    public function update(
        UpdateContactSettingsRequest $request,
        SaveContactDetails $save,
    ): RedirectResponse {
        $details = ContactDetails::fromValues(
            email: $request->validated('email'),
            phone: $request->validated('phone'),
        );

        $save->handle($details, AdminActor::fromAdmin($request->user('admin')));

        return redirect()
            ->route('admin.contact.edit')
            ->with('status', 'Datos de contacto guardados. Los enlaces sociales se administran por separado.');
    }

    public function createLink(): View
    {
        return view('admin.contact.link-form', [
            'link' => null,
        ]);
    }

    public function storeLink(SocialLinkRequest $request, SaveSocialLink $save): RedirectResponse
    {
        $save->handle(null, $this->inputFrom($request), $this->actor($request));

        return redirect()->route('admin.contact.edit')->with('status', 'Enlace social agregado.');
    }

    public function editLink(int $link, ContactRepository $contact): View
    {
        return view('admin.contact.link-form', [
            'link' => $this->find($link, $contact),
        ]);
    }

    public function updateLink(
        SocialLinkRequest $request,
        int $link,
        ContactRepository $contact,
        SaveSocialLink $save,
    ): RedirectResponse {
        $existing = $this->find($link, $contact);

        $save->handle($existing, $this->inputFrom($request), $this->actor($request));

        return redirect()->route('admin.contact.edit')->with('status', 'Enlace social actualizado.');
    }

    public function confirmDeleteLink(int $link, ContactRepository $contact): View
    {
        return view('admin.contact.link-delete', [
            'link' => $this->find($link, $contact),
        ]);
    }

    public function destroyLink(
        int $link,
        ContactRepository $contact,
        DeleteSocialLink $delete,
    ): RedirectResponse {
        $delete->handle($this->find($link, $contact), AdminActor::fromAdmin(request()->user('admin')));

        return redirect()->route('admin.contact.edit')->with('status', 'Enlace social eliminado.');
    }

    private function find(int $id, ContactRepository $contact): SocialLink
    {
        return $contact->findSocialLink($id) ?? abort(404);
    }

    private function inputFrom(SocialLinkRequest $request): SocialLinkInput
    {
        return new SocialLinkInput(
            label: (string) $request->validated('label'),
            url: (string) $request->validated('url'),
            sortOrder: (int) ($request->validated('sort_order') ?? 0),
            isActive: $request->boolean('is_active'),
        );
    }

    private function actor(SocialLinkRequest $request): AdminActor
    {
        return AdminActor::fromAdmin($request->user('admin'));
    }
}
