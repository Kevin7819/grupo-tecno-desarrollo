<?php

namespace App\Http\Controllers\Admin;

use App\Application\Cms\AdminActor;
use App\Application\Cms\DeleteGalleryImage;
use App\Application\Cms\GalleryImageInput;
use App\Application\Cms\GalleryRepository;
use App\Application\Cms\SaveGalleryImage;
use App\Domain\Cms\GalleryImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryImageRequest;
use App\Infrastructure\Storage\PublicImageStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class GalleryController extends Controller
{
    public function index(GalleryRepository $images): View
    {
        return view('admin.gallery.index', [
            'images' => $images->all(),
        ]);
    }

    public function create(): View
    {
        return view('admin.gallery.form', [
            'image' => null,
        ]);
    }

    public function store(
        GalleryImageRequest $request,
        SaveGalleryImage $save,
        PublicImageStorage $images,
    ): RedirectResponse {
        $input = new GalleryImageInput(
            title: (string) $request->validated('title'),
            imagePath: $images->store($request->file('image'), 'gallery'),
            altText: (string) $request->validated('alt_text'),
            caption: $request->validated('caption'),
            sortOrder: (int) ($request->validated('sort_order') ?? 0),
            isPublished: $request->boolean('is_published'),
        );

        $save->handle(null, $input, $this->actor($request));

        return redirect()->route('admin.gallery.index')->with('status', 'Imagen agregada a la galería.');
    }

    public function edit(int $image, GalleryRepository $images): View
    {
        return view('admin.gallery.form', [
            'image' => $this->find($image, $images),
        ]);
    }

    public function update(
        GalleryImageRequest $request,
        int $image,
        GalleryRepository $images,
        SaveGalleryImage $save,
        PublicImageStorage $storage,
    ): RedirectResponse {
        $existing = $this->find($image, $images);

        $imagePath = $existing->imagePath;
        $newPath = null;

        if ($request->hasFile('image')) {
            $newPath = $storage->store($request->file('image'), 'gallery');
            $imagePath = $newPath;
        }

        $input = new GalleryImageInput(
            title: (string) $request->validated('title'),
            imagePath: $imagePath,
            altText: (string) $request->validated('alt_text'),
            caption: $request->validated('caption'),
            sortOrder: (int) ($request->validated('sort_order') ?? 0),
            isPublished: $request->boolean('is_published'),
        );

        $save->handle($existing, $input, $this->actor($request));

        if ($newPath !== null) {
            $storage->replace($existing->imagePath, $newPath);
        }

        return redirect()->route('admin.gallery.index')->with('status', 'Imagen actualizada.');
    }

    public function confirmDelete(int $image, GalleryRepository $images): View
    {
        return view('admin.gallery.delete', [
            'image' => $this->find($image, $images),
        ]);
    }

    public function destroy(
        int $image,
        GalleryRepository $images,
        DeleteGalleryImage $delete,
        PublicImageStorage $storage,
    ): RedirectResponse {
        $existing = $this->find($image, $images);

        $delete->handle($existing, AdminActor::fromAdmin(request()->user('admin')));
        $storage->delete($existing->imagePath);

        return redirect()->route('admin.gallery.index')->with('status', 'Imagen eliminada.');
    }

    private function find(int $id, GalleryRepository $images): GalleryImage
    {
        return $images->find($id) ?? abort(404);
    }

    private function actor(GalleryImageRequest $request): AdminActor
    {
        return AdminActor::fromAdmin($request->user('admin'));
    }
}
