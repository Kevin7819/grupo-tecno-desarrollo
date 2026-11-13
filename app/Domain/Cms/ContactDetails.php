<?php

namespace App\Domain\Cms;

/**
 * Public contact details stored by the administrator.
 *
 * Values are validated when they are read too, so a corrupted row never
 * produces a link that cannot work.
 */
final readonly class ContactDetails
{
    private function __construct(
        public ?string $email,
        public ?string $phone,
    ) {}

    public static function fromValues(?string $email, ?string $phone): self
    {
        $email = trim((string) $email);
        $phone = trim((string) $phone);

        return new self(
            email: self::isValidEmail($email) ? $email : null,
            phone: self::isValidPhone($phone) ? $phone : null,
        );
    }

    public static function empty(): self
    {
        return new self(null, null);
    }

    public function isEmpty(): bool
    {
        return $this->email === null && $this->phone === null;
    }

    public function mailto(): ?string
    {
        return $this->email !== null ? 'mailto:'.$this->email : null;
    }

    public function tel(): ?string
    {
        if ($this->phone === null) {
            return null;
        }

        $digits = preg_replace('/[^0-9+]/', '', $this->phone);

        return $digits !== '' ? 'tel:'.$digits : null;
    }

    private static function isValidEmail(string $email): bool
    {
        return $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private static function isValidPhone(string $phone): bool
    {
        return $phone !== '' && preg_match('/^[0-9+()\-\s.]{6,30}$/', $phone) === 1;
    }
}
