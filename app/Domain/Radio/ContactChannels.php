<?php

namespace App\Domain\Radio;

/**
 * Public contact channels of the station.
 *
 * Values come from configuration and are untrusted: only valid e-mail
 * addresses and https:// URLs reach the views, so the site never renders a
 * channel that cannot work or that points to a non-public scheme.
 */
final readonly class ContactChannels
{
    /**
     * @param  array<string, string>  $social  Display label => validated https URL.
     */
    private function __construct(
        public ?string $email,
        public array $social,
        public ?string $phone = null,
    ) {}

    /**
     * @param  array<array-key, mixed>  $social
     */
    public static function fromValues(?string $email, array $social, ?string $phone = null): self
    {
        $email = trim((string) $email);
        $phone = trim((string) $phone);

        return new self(
            email: self::isValidEmail($email) ? $email : null,
            social: self::validSocialLinks($social),
            phone: self::isValidPhone($phone) ? $phone : null,
        );
    }

    public function hasEmail(): bool
    {
        return $this->email !== null;
    }

    public function hasPhone(): bool
    {
        return $this->phone !== null;
    }

    public function tel(): ?string
    {
        if ($this->phone === null) {
            return null;
        }

        $digits = preg_replace('/[^0-9+]/', '', $this->phone);

        return $digits !== '' ? 'tel:'.$digits : null;
    }

    public function hasSocial(): bool
    {
        return $this->social !== [];
    }

    public function isEmpty(): bool
    {
        return ! $this->hasEmail() && ! $this->hasPhone() && ! $this->hasSocial();
    }

    public function mailto(): ?string
    {
        return $this->email !== null ? 'mailto:'.$this->email : null;
    }

    /**
     * @param  array<array-key, mixed>  $social
     * @return array<string, string>
     */
    private static function validSocialLinks(array $social): array
    {
        $valid = [];

        foreach ($social as $label => $url) {
            $label = trim((string) $label);

            if ($label === '' || ! is_string($url) || ! self::isValidUrl(trim($url))) {
                continue;
            }

            $valid[$label] = trim($url);
        }

        return $valid;
    }

    private static function isValidEmail(string $email): bool
    {
        return $email !== ''
            && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private static function isValidPhone(string $phone): bool
    {
        return $phone !== '' && preg_match('/^[0-9+()\-\s.]{6,30}$/', $phone) === 1;
    }

    private static function isValidUrl(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $scheme = mb_strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = (string) parse_url($url, PHP_URL_HOST);

        return $scheme === 'https' && $host !== '';
    }
}
