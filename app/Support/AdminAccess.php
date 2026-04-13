<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

final class AdminAccess
{
    private const GUARD_NAME = 'web';

    private const CANONICAL_ROLE_NAMES = [
        'Super Admin',
        'Admin',
        'Editor',
        'Manager',
        'Staff',
    ];

    private const ROLE_ALIASES = [
        'super admin' => 'Super Admin',
        'admin' => 'Admin',
        'editor' => 'Editor',
        'manager' => 'Manager',
        'staff' => 'Staff',
    ];

    public static function guardName(): string
    {
        return self::GUARD_NAME;
    }

    public static function canonicalRoleNames(): array
    {
        return self::CANONICAL_ROLE_NAMES;
    }

    public static function allowedRoleNames(): array
    {
        return self::queryableRoleNames();
    }

    public static function queryableRoleNames(): array
    {
        $names = [];

        foreach (self::CANONICAL_ROLE_NAMES as $role) {
            $normalized = Str::lower($role);

            $names[] = $role;
            $names[] = $normalized;
            $names[] = str_replace(' ', '_', $normalized);
            $names[] = str_replace(' ', '-', $normalized);
        }

        foreach (array_keys(self::ROLE_ALIASES) as $alias) {
            $names[] = $alias;
            $names[] = str_replace(' ', '_', $alias);
            $names[] = str_replace(' ', '-', $alias);
        }

        return array_values(array_unique($names));
    }

    public static function normalizeRoleName(string $role): string
    {
        $trimmed = trim($role);
        $normalizedKey = Str::lower(str_replace(['_', '-'], ' ', $trimmed));

        return self::ROLE_ALIASES[$normalizedKey] ?? $trimmed;
    }

    public static function isAdminRoleName(string $role): bool
    {
        return in_array(self::normalizeRoleName($role), self::CANONICAL_ROLE_NAMES, true);
    }

    public static function canAccess(?Authenticatable $user): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        $roles = $user->getRoleNames()->map(
            static fn (string $role): string => self::normalizeRoleName($role)
        );

        return $roles->intersect(self::CANONICAL_ROLE_NAMES)->isNotEmpty();
    }
}
