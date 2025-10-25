<?php

namespace App\Domain\Shared\ValueObjects;

use Illuminate\Support\Str;

class Slug
{
    public function __construct(
        private readonly string $value
    ) {
        if (empty($value)) {
            throw new \InvalidArgumentException('Slug cannot be empty');
        }

        if (!preg_match('/^[a-z0-9-]+$/', $value)) {
            throw new \InvalidArgumentException('Slug must contain only lowercase letters, numbers, and hyphens');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Slug $other): bool
    {
        return $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        return new self(Str::slug($value));
    }

    public static function fromTitle(string $title): self
    {
        return new self(Str::slug($title));
    }
}
