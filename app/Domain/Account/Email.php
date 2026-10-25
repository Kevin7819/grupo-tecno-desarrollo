<?php

namespace App\Domain\Account;

final readonly class Email
{
    private function __construct(public string $value) {}

    public static function fromString(string $value): self
    {
        return new self(mb_strtolower(trim($value)));
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
