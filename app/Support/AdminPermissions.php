<?php

namespace App\Support;

final class AdminPermissions
{
    public const MANAGE_USERS = 'manage users';

    public const MANAGE_ROLES = 'manage roles';

    public const MANAGE_DOCUMENTS = 'manage documents';

    public const MANAGE_PRODUCTS = 'manage products';

    public const MANAGE_CONTENT = 'manage content';

    public const MANAGE_ORDERS = 'manage orders';

    public const MANAGE_COUPONS = 'manage coupons';

    public const MANAGE_SYSTEM = 'manage system';

    private const ALL = [
        self::MANAGE_USERS,
        self::MANAGE_ROLES,
        self::MANAGE_DOCUMENTS,
        self::MANAGE_PRODUCTS,
        self::MANAGE_CONTENT,
        self::MANAGE_ORDERS,
        self::MANAGE_COUPONS,
        self::MANAGE_SYSTEM,
    ];

    private const DEFAULT_ROLE_PERMISSIONS = [
        'Super Admin' => self::ALL,
        'Admin' => self::ALL,
        'Editor' => [
            self::MANAGE_PRODUCTS,
            self::MANAGE_CONTENT,
        ],
        'Manager' => [
            self::MANAGE_PRODUCTS,
            self::MANAGE_ORDERS,
            self::MANAGE_COUPONS,
            self::MANAGE_DOCUMENTS,
        ],
        'Staff' => [],
    ];

    public static function all(): array
    {
        return self::ALL;
    }

    public static function defaultRolePermissions(): array
    {
        return self::DEFAULT_ROLE_PERMISSIONS;
    }

    public static function defaultsForRole(string $role): array
    {
        return self::DEFAULT_ROLE_PERMISSIONS[AdminAccess::normalizeRoleName($role)] ?? [];
    }

    public static function isCanonical(string $permission): bool
    {
        return in_array(trim($permission), self::ALL, true);
    }
}
