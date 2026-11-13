<?php

namespace App\Infrastructure\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stores public images (station logo, program covers, host photos) on the
 * `public` disk. Paths are relative and never include the disk prefix.
 *
 * `store()` only writes the new file: deleting the previous one is the
 * caller's decision, so a rejected form never loses the current image.
 */
final class PublicImageStorage
{
    public function store(UploadedFile $file, string $directory): string
    {
        return (string) $file->store($directory, 'public');
    }

    public function delete(?string $path): void
    {
        if ($path === null || trim($path) === '') {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    public function replace(?string $previousPath, ?string $newPath): void
    {
        if ($previousPath !== null && $previousPath !== $newPath) {
            $this->delete($previousPath);
        }
    }
}
