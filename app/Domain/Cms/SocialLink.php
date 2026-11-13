<?php

namespace App\Domain\Cms;

/**
 * A public social profile link managed from the admin panel.
 */
final readonly class SocialLink
{
    private function __construct(
        public ?int $id,
        public string $label,
        public string $url,
        public int $sortOrder,
        public bool $isActive,
    ) {}

    public static function fromValues(
        ?int $id,
        ?string $label,
        ?string $url,
        int $sortOrder = 0,
        bool $isActive = true,
    ): self {
        return new self(
            id: $id,
            label: trim((string) $label),
            url: trim((string) $url),
            sortOrder: max(0, $sortOrder),
            isActive: $isActive,
        );
    }

    public function isPublic(): bool
    {
        return $this->isActive && self::isValidUrl($this->url);
    }

    public static function isValidUrl(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $scheme = mb_strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = (string) parse_url($url, PHP_URL_HOST);

        return $scheme === 'https' && $host !== '';
    }
}
