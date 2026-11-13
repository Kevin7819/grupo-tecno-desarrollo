<?php

namespace App\Http\Controllers\Admin;

use App\Application\Cms\AdminActor;
use App\Application\Cms\DeleteHost;
use App\Application\Cms\HostInput;
use App\Application\Cms\HostRepository;
use App\Application\Cms\SaveHost;
use App\Domain\Cms\Host;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HostRequest;
use App\Infrastructure\Storage\PublicImageStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class HostController extends Controller
{
    public function index(HostRepository $hosts): View
    {
        return view('admin.hosts.index', [
            'hosts' => $hosts->all(),
        ]);
    }

    public function create(): View
    {
        return view('admin.hosts.form', [
            'host' => null,
        ]);
    }

    public function store(HostRequest $request, SaveHost $save, PublicImageStorage $images): RedirectResponse
    {
        $save->handle(null, $this->inputFrom($request, $images), $this->actor($request));

        return redirect()->route('admin.hosts.index')->with('status', 'Locutor creado.');
    }

    public function edit(int $host, HostRepository $hosts): View
    {
        return view('admin.hosts.form', [
            'host' => $this->find($host, $hosts),
        ]);
    }

    public function update(
        HostRequest $request,
        int $host,
        HostRepository $hosts,
        SaveHost $save,
        PublicImageStorage $images,
    ): RedirectResponse {
        $existing = $this->find($host, $hosts);
        $input = $this->inputFrom($request, $images, $existing);

        $save->handle($existing, $input, $this->actor($request));

        $images->replace($existing->photoPath, $input->photoPath);

        return redirect()->route('admin.hosts.index')->with('status', 'Locutor actualizado.');
    }

    public function confirmDelete(int $host, HostRepository $hosts): View
    {
        return view('admin.hosts.delete', [
            'host' => $this->find($host, $hosts),
        ]);
    }

    public function destroy(
        int $host,
        HostRepository $hosts,
        DeleteHost $delete,
        PublicImageStorage $images,
        Request $request,
    ): RedirectResponse {
        $existing = $this->find($host, $hosts);

        $delete->handle($existing, AdminActor::fromAdmin($request->user('admin')));
        $images->delete($existing->photoPath);

        return redirect()->route('admin.hosts.index')->with('status', 'Locutor eliminado.');
    }

    private function find(int $id, HostRepository $hosts): Host
    {
        return $hosts->find($id) ?? abort(404);
    }

    private function actor(HostRequest $request): AdminActor
    {
        return AdminActor::fromAdmin($request->user('admin'));
    }

    private function inputFrom(
        HostRequest $request,
        PublicImageStorage $images,
        ?Host $existing = null,
    ): HostInput {
        $photoPath = $existing?->photoPath;

        if ($request->hasFile('photo')) {
            $photoPath = $images->store($request->file('photo'), 'hosts');
        } elseif ($request->boolean('remove_photo')) {
            $photoPath = null;
        }

        return new HostInput(
            name: (string) $request->validated('name'),
            bio: $request->validated('bio'),
            photoPath: $photoPath,
            isActive: $request->boolean('is_active'),
        );
    }
}
