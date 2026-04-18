<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AuthorizationSecurityService
{
    private const ROLE_PERMISSION_CACHE_REGISTRY_PREFIX = 'authorization.role_permission_checks.';
    private array $roleHierarchy = [];
    private array $permissionGroups = [];
    private int $cacheTtl = 3600; // 1 hour

    public function __construct()
    {
        $this->initializeRoleHierarchy();
        $this->initializePermissionGroups();
    }

    private function initializeRoleHierarchy(): void
    {
        $this->roleHierarchy = [
            'super_admin' => ['admin', 'manager', 'staff', 'customer'],
            'admin' => ['manager', 'staff', 'customer'],
            'manager' => ['staff', 'customer'],
            'staff' => ['customer'],
            'customer' => [],
        ];
    }

    private function initializePermissionGroups(): void
    {
        $this->permissionGroups = [
            'products' => [
                'view_products',
                'create_products',
                'edit_products',
                'delete_products',
                'publish_products',
                'manage_product_categories',
                'manage_product_brands',
            ],
            'orders' => [
                'view_orders',
                'create_orders',
                'edit_orders',
                'delete_orders',
                'process_orders',
                'cancel_orders',
                'refund_orders',
            ],
            'customers' => [
                'view_customers',
                'create_customers',
                'edit_customers',
                'delete_customers',
                'manage_customer_data',
                'view_customer_orders',
            ],
            'inventory' => [
                'view_inventory',
                'manage_inventory',
                'update_stock',
                'manage_suppliers',
                'view_reports',
            ],
            'marketing' => [
                'manage_coupons',
                'manage_discounts',
                'send_emails',
                'manage_campaigns',
                'view_analytics',
            ],
            'system' => [
                'manage_users',
                'manage_roles',
                'manage_permissions',
                'view_logs',
                'system_settings',
                'backup_data',
            ],
        ];
    }

    public function checkPermission(string $userRole, string $permission): bool
    {
        $cacheKey = "permission_check_{$userRole}_{$permission}";
        $this->trackRolePermissionCacheKey($userRole, $cacheKey);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($userRole, $permission) {
            // Check direct permission
            if ($this->hasDirectPermission($userRole, $permission)) {
                return true;
            }

            // Check inherited permissions through role hierarchy
            return $this->hasInheritedPermission($userRole, $permission);
        });
    }

    private function hasDirectPermission(string $role, string $permission): bool
    {
        try {
            $roleModel = Role::where('name', $role)
                ->where('guard_name', $this->guardName())
                ->first();
            if (!$roleModel) {
                return false;
            }

            return $roleModel->hasPermissionTo($permission);
        } catch (\Exception $e) {
            Log::error('Error checking direct permission', [
                'role' => $role,
                'permission' => $permission,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function hasInheritedPermission(string $role, string $permission): bool
    {
        $inheritedRoles = $this->roleHierarchy[$role] ?? [];
        
        foreach ($inheritedRoles as $inheritedRole) {
            if ($this->hasDirectPermission($inheritedRole, $permission)) {
                return true;
            }
            
            // Recursively check inherited roles
            if ($this->hasInheritedPermission($inheritedRole, $permission)) {
                return true;
            }
        }

        return false;
    }

    public function checkMultiplePermissions(string $userRole, array $permissions): array
    {
        $results = [];
        
        foreach ($permissions as $permission) {
            $results[$permission] = $this->checkPermission($userRole, $permission);
        }

        return $results;
    }

    public function checkAnyPermission(string $userRole, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->checkPermission($userRole, $permission)) {
                return true;
            }
        }

        return false;
    }

    public function checkAllPermissions(string $userRole, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->checkPermission($userRole, $permission)) {
                return false;
            }
        }

        return true;
    }

    public function getRolePermissions(string $role): array
    {
        $cacheKey = "role_permissions_{$role}";
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($role) {
            try {
                $roleModel = Role::where('name', $role)
                    ->where('guard_name', $this->guardName())
                    ->first();
                if (!$roleModel) {
                    return [];
                }

                $permissions = $roleModel->permissions->pluck('name')->toArray();
                
                // Add inherited permissions
                $inheritedPermissions = $this->getInheritedPermissions($role);
                $permissions = array_unique(array_merge($permissions, $inheritedPermissions));

                return $permissions;
            } catch (\Exception $e) {
                Log::error('Error getting role permissions', [
                    'role' => $role,
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        });
    }

    private function getInheritedPermissions(string $role): array
    {
        $inheritedRoles = $this->roleHierarchy[$role] ?? [];
        $permissions = [];

        foreach ($inheritedRoles as $inheritedRole) {
            $rolePermissions = $this->getRolePermissions($inheritedRole);
            $permissions = array_merge($permissions, $rolePermissions);
        }

        return array_unique($permissions);
    }

    public function getPermissionGroups(): array
    {
        return $this->permissionGroups;
    }

    public function getPermissionsByGroup(string $group): array
    {
        return $this->permissionGroups[$group] ?? [];
    }

    public function assignRoleToUser($user, string $role): bool
    {
        try {
            $user->assignRole($role);
            
            Log::info('Role assigned to user', [
                'user_id' => $user->id,
                'role' => $role,
                'timestamp' => now(),
            ]);

            // Clear permission cache
            $this->clearPermissionCache($user->id);

            return true;
        } catch (\Exception $e) {
            Log::error('Error assigning role to user', [
                'user_id' => $user->id,
                'role' => $role,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function removeRoleFromUser($user, string $role): bool
    {
        try {
            $user->removeRole($role);
            
            Log::info('Role removed from user', [
                'user_id' => $user->id,
                'role' => $role,
                'timestamp' => now(),
            ]);

            // Clear permission cache
            $this->clearPermissionCache($user->id);

            return true;
        } catch (\Exception $e) {
            Log::error('Error removing role from user', [
                'user_id' => $user->id,
                'role' => $role,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function grantPermissionToRole(string $role, string $permission): bool
    {
        try {
            $roleModel = Role::where('name', $role)
                ->where('guard_name', $this->guardName())
                ->first();
            if (!$roleModel) {
                return false;
            }

            $permissionModel = Permission::where('name', $permission)
                ->where('guard_name', $this->guardName())
                ->first();
            if (!$permissionModel) {
                $permissionModel = Permission::create([
                    'name' => $permission,
                    'guard_name' => $this->guardName(),
                ]);
            }

            $roleModel->givePermissionTo($permissionModel);
            
            Log::info('Permission granted to role', [
                'role' => $role,
                'permission' => $permission,
                'timestamp' => now(),
            ]);

            // Clear role permission cache
            $this->clearRolePermissionCache($role);

            return true;
        } catch (\Exception $e) {
            Log::error('Error granting permission to role', [
                'role' => $role,
                'permission' => $permission,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function revokePermissionFromRole(string $role, string $permission): bool
    {
        try {
            $roleModel = Role::where('name', $role)
                ->where('guard_name', $this->guardName())
                ->first();
            if (!$roleModel) {
                return false;
            }

            $permissionModel = Permission::where('name', $permission)
                ->where('guard_name', $this->guardName())
                ->first();
            if (! $permissionModel) {
                return false;
            }

            $roleModel->revokePermissionTo($permissionModel);
            
            Log::info('Permission revoked from role', [
                'role' => $role,
                'permission' => $permission,
                'timestamp' => now(),
            ]);

            // Clear role permission cache
            $this->clearRolePermissionCache($role);

            return true;
        } catch (\Exception $e) {
            Log::error('Error revoking permission from role', [
                'role' => $role,
                'permission' => $permission,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function createRole(string $name, array $permissions = []): bool
    {
        try {
            $role = Role::create([
                'name' => $name,
                'guard_name' => $this->guardName(),
            ]);
            
            if (!empty($permissions)) {
                foreach ($permissions as $permission) {
                    $permissionModel = Permission::where('name', $permission)
                        ->where('guard_name', $this->guardName())
                        ->first();
                    if (!$permissionModel) {
                        $permissionModel = Permission::create([
                            'name' => $permission,
                            'guard_name' => $this->guardName(),
                        ]);
                    }
                    $role->givePermissionTo($permissionModel);
                }
            }

            Log::info('Role created', [
                'role' => $name,
                'permissions' => $permissions,
                'timestamp' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error creating role', [
                'role' => $name,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function deleteRole(string $name): bool
    {
        try {
            $role = Role::where('name', $name)
                ->where('guard_name', $this->guardName())
                ->first();
            if (!$role) {
                return false;
            }

            $role->delete();
            
            Log::info('Role deleted', [
                'role' => $name,
                'timestamp' => now(),
            ]);

            // Clear role permission cache
            $this->clearRolePermissionCache($name);

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting role', [
                'role' => $name,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function auditUserAccess($user, string $action, string $resource = null): void
    {
        Log::info('User access audit', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'action' => $action,
            'resource' => $resource,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }

    public function checkResourceAccess($user, string $resource, string $action): bool
    {
        $userRoles = $user->getRoleNames()->toArray();
        
        foreach ($userRoles as $role) {
            if ($this->checkPermission($role, "{$action}_{$resource}")) {
                return true;
            }
        }

        return false;
    }

    public function getAccessibleResources($user): array
    {
        $userRoles = $user->getRoleNames()->toArray();
        $accessibleResources = [];

        foreach ($userRoles as $role) {
            $permissions = $this->getRolePermissions($role);
            
            foreach ($permissions as $permission) {
                if (preg_match('/^(view|create|edit|delete)_(.+)$/', $permission, $matches)) {
                    $action = $matches[1];
                    $resource = $matches[2];
                    
                    if (!isset($accessibleResources[$resource])) {
                        $accessibleResources[$resource] = [];
                    }
                    
                    $accessibleResources[$resource][] = $action;
                }
            }
        }

        return $accessibleResources;
    }

    private function clearPermissionCache(int $userId): void
    {
        $cacheKeys = [
            "user_permissions_{$userId}",
            "user_roles_{$userId}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    private function clearRolePermissionCache(string $role): void
    {
        Cache::forget("role_permissions_{$role}");

        $registryKey = $this->rolePermissionRegistryKey($role);
        $cacheKeys = Cache::get($registryKey, []);

        if (! is_array($cacheKeys)) {
            $cacheKeys = [];
        }

        foreach (array_unique($cacheKeys) as $cacheKey) {
            Cache::forget($cacheKey);
        }

        Cache::forget($registryKey);
    }

    public function getSecurityStatistics(): array
    {
        try {
            $guard = $this->guardName();
            $totalRoles = Role::where('guard_name', $guard)->count();
            $totalPermissions = Permission::where('guard_name', $guard)->count();
            $rolePermissions = Role::with('permissions')
                ->where('guard_name', $guard)
                ->get();

            $statistics = [
                'total_roles' => $totalRoles,
                'total_permissions' => $totalPermissions,
                'roles_with_permissions' => $rolePermissions->map(function ($role) {
                    return [
                        'role' => $role->name,
                        'permission_count' => $role->permissions->count(),
                    ];
                })->toArray(),
            ];

            return $statistics;
        } catch (\Exception $e) {
            Log::error('Error getting security statistics', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function validateRoleHierarchy(): array
    {
        $issues = [];

        foreach ($this->roleHierarchy as $parentRole => $childRoles) {
            foreach ($childRoles as $childRole) {
                if (!isset($this->roleHierarchy[$childRole])) {
                    $issues[] = "Child role '{$childRole}' not found in hierarchy";
                }
            }
        }

        return [
            'is_valid' => empty($issues),
            'issues' => $issues,
        ];
    }

    private function guardName(): string
    {
        return (string) (config('permission.defaults.guard') ?? config('auth.defaults.guard', 'web'));
    }

    private function trackRolePermissionCacheKey(string $role, string $cacheKey): void
    {
        $registryKey = $this->rolePermissionRegistryKey($role);
        $keys = Cache::get($registryKey, []);

        if (! is_array($keys)) {
            $keys = [];
        }

        if (! in_array($cacheKey, $keys, true)) {
            $keys[] = $cacheKey;
            Cache::forever($registryKey, array_values($keys));
        }
    }

    private function rolePermissionRegistryKey(string $role): string
    {
        return self::ROLE_PERMISSION_CACHE_REGISTRY_PREFIX . $role;
    }
}
