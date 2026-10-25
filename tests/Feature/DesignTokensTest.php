<?php

namespace Tests\Feature;

use Tests\TestCase;

final class DesignTokensTest extends TestCase
{
    public function test_the_approved_palette_is_centralized_in_the_theme_tokens(): void
    {
        $css = (string) file_get_contents(resource_path('css/app.css'));

        foreach (['#0D0E12', '#14171F', '#232733', '#FF5528', '#F3F4F6', '#9CA3AF'] as $hex) {
            $this->assertStringContainsStringIgnoringCase(
                $hex,
                $css,
                "The palette token [{$hex}] is missing from resources/css/app.css.",
            );
        }
    }

    public function test_accessibility_baselines_are_present_in_the_stylesheet(): void
    {
        $css = (string) file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString(':focus-visible', $css);
        $this->assertStringContainsString('prefers-reduced-motion', $css);
        $this->assertStringContainsString('color-scheme: dark', $css);
    }

    public function test_primary_buttons_use_dark_text_over_the_brand_color(): void
    {
        $button = (string) file_get_contents(
            resource_path('views/components/ui/button.blade.php'),
        );

        $this->assertStringContainsString('bg-brand text-surface-base', $button);
    }
}
