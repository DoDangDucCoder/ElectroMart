<?php

namespace App\Domain\Customer\Entities;

use App\Domain\Shared\ValueObjects\Money;

class Customer
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly ?string $name = null,
        public readonly ?string $phone = null,
        public readonly ?string $avatar = null,
        public readonly bool $isActive = true,
        public readonly bool $emailVerified = false,
        public readonly bool $phoneVerified = false,
        public readonly ?\DateTimeImmutable $emailVerifiedAt = null,
        public readonly ?\DateTimeImmutable $phoneVerifiedAt = null,
        public readonly ?int $loyaltyTierId = null,
        public readonly float $loyaltyPointsBalance = 0.0,
        public readonly ?array $meta = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    public function getMetaValue(string $key, mixed $default = null): mixed
    {
        return $this->meta[$key] ?? $default;
    }

    public function isVerified(): bool
    {
        return $this->emailVerified;
    }

    public function hasPhone(): bool
    {
        return !empty($this->phone);
    }

    public function hasLoyaltyTier(): bool
    {
        return $this->loyaltyTierId !== null;
    }

    public function hasLoyaltyPoints(): bool
    {
        return $this->loyaltyPointsBalance > 0;
    }

    public function getDisplayName(): string
    {
        return $this->name ?? $this->email;
    }

    public function canEarnPoints(): bool
    {
        return $this->isActive && $this->isVerified();
    }
}
