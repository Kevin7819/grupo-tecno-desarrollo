<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

final class BrandAssetsTest extends TestCase
{
    /**
     * Public brand files the layouts reference. Keep in sync with the views.
     *
     * @var list<string>
     */
    private const BRAND_ASSETS = [
        'Logo.jpg',
        'Logo-mark.png',
        'favicon.ico',
        'apple-touch-icon.png',
    ];

    public function test_every_referenced_brand_asset_exists_and_is_not_empty(): void
    {
        foreach (self::BRAND_ASSETS as $asset) {
            $path = public_path($asset);

            $this->assertFileExists($path, "Missing public brand asset [{$asset}].");
            $this->assertGreaterThan(
                0,
                (int) filesize($path),
                "The public brand asset [{$asset}] is empty, so the browser gets a broken response.",
            );
        }
    }

    public function test_the_retired_png_logo_is_not_referenced_anymore(): void
    {
        $offenders = [];

        foreach (File::allFiles(resource_path('views')) as $file) {
            if (str_contains((string) file_get_contents($file->getPathname()), 'Logo.png')) {
                $offenders[] = $file->getRelativePathname();
            }
        }

        $this->assertSame(
            [],
            $offenders,
            'These views still reference the removed public/Logo.png: '.implode(', ', $offenders),
        );
    }

    public function test_the_public_and_guest_layouts_render_the_new_logo_and_icon_set(): void
    {
        foreach (['/', '/login'] as $uri) {
            $response = $this->get($uri);

            $response->assertOk();
            $response->assertSee('rel="icon"', false);
            $response->assertSee('/favicon.ico', false);
            $response->assertSee('rel="apple-touch-icon"', false);
            $response->assertSee('/apple-touch-icon.png', false);
            $response->assertSee('/Logo-mark.png', false);
            $response->assertDontSee('/Logo.png', false);
        }
    }

    public function test_the_home_page_exposes_social_metadata_for_the_logo(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('property="og:image"', false);
        $response->assertSee('/Logo.jpg', false);
        $response->assertSee('property="og:image:width"', false);
        $response->assertSee('name="twitter:card"', false);
        $response->assertSee('name="twitter:image"', false);
    }

    public function test_the_favicon_is_a_real_icon_container(): void
    {
        $header = bin2hex((string) file_get_contents(public_path('favicon.ico'), false, null, 0, 4));

        $this->assertSame('00000100', $header, 'public/favicon.ico is not a valid ICO container.');
    }

    public function test_the_generated_png_assets_are_png_files(): void
    {
        foreach (['Logo-mark.png', 'apple-touch-icon.png'] as $asset) {
            $signature = bin2hex((string) file_get_contents(public_path($asset), false, null, 0, 8));

            $this->assertSame(
                '89504e470d0a1a0a',
                $signature,
                "The generated asset [{$asset}] is not a PNG file.",
            );
        }
    }

    public function test_the_original_logo_is_kept_untouched_at_its_original_size(): void
    {
        $size = getimagesize(public_path('Logo.jpg'));

        $this->assertIsArray($size, 'public/Logo.jpg is not a readable image.');
        $this->assertSame('image/jpeg', $size['mime']);
        $this->assertSame(1024, $size[0], 'The original logo width changed.');
        $this->assertSame(1024, $size[1], 'The original logo height changed.');
    }
}
