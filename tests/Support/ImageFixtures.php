<?php

namespace Tests\Support;

use Illuminate\Http\UploadedFile;

/**
 * Minimal real image files for upload tests.
 *
 * `UploadedFile::fake()->image()` needs the GD extension, which is not
 * installed in this environment. These fixtures are real 1x1 PNG/JPEG files,
 * so the `image` and `mimes` validation rules run against actual image bytes.
 */
final class ImageFixtures
{
    private const PNG_1X1 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    public static function png(string $name = 'image.png'): UploadedFile
    {
        return self::file($name, self::PNG_1X1);
    }

    private static function file(string $name, string $base64): UploadedFile
    {
        $path = (string) tempnam(sys_get_temp_dir(), 'radio-image-');
        file_put_contents($path, (string) base64_decode($base64, true));

        return new UploadedFile($path, $name, 'image/png', null, true);
    }
}
