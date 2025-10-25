<?php

namespace App\Domain\Customer\DTO;

use Illuminate\Support\Arr;

class CustomerData
{
    public function __construct(
        public readonly string $email,
        public readonly ?string $name = null,
        public readonly ?string $phone = null,
        public readonly ?string $avatar = null,
        public readonly bool $isActive = true,
        public readonly ?array $meta = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            name: $data['name'] ?? null,
            phone: $data['phone'] ?? null,
            avatar: $data['avatar'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
            meta: $data['meta'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'is_active' => $this->isActive,
            'meta' => $this->meta,
        ];
    }
}
