<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class NormalizeAdminAuthCommand extends Command
{
    protected $signature = 'admin:normalize-auth {--dry-run : Show the changes without saving them}';

    protected $description = 'Normalize admin roles to the web guard and backfill legacy admin users into users';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info($dryRun ? 'Dry-running admin auth normalization...' : 'Normalizing admin auth...');

        $this->normalizeAdminRoles($dryRun);
        $this->normalizePermissions($dryRun);
        $this->ensureCanonicalRoles($dryRun);
        $this->ensureCanonicalPermissions($dryRun);
        $this->syncCanonicalRolePermissions($dryRun);
        $this->backfillLegacyAdmins($dryRun);

        if (! $dryRun) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        $this->info($dryRun ? 'Dry run complete.' : 'Admin auth normalization complete.');

        return self::SUCCESS;
    }

    protected function normalizeAdminRoles(bool $dryRun): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        $legacyRoles = Role::query()
            ->where('guard_name', '!=', AdminAccess::guardName())
            ->get()
            ->filter(fn (Role $role): bool => AdminAccess::isAdminRoleName($role->name));

        foreach ($legacyRoles as $legacyRole) {
            $canonicalName = AdminAccess::normalizeRoleName($legacyRole->name);
            $existingWebRole = Role::query()
                ->where('name', $canonicalName)
                ->where('guard_name', AdminAccess::guardName())
                ->first();

            if ($existingWebRole) {
                $this->line("Merging role {$legacyRole->name} ({$legacyRole->id}) into {$existingWebRole->name} ({$existingWebRole->id})");

                if ($dryRun) {
                    continue;
                }

                $this->mergeRoleAssignments($legacyRole->id, $existingWebRole->id);
                $legacyRole->delete();
                continue;
            }

            $this->line("Updating role {$legacyRole->name} ({$legacyRole->id}) to guard " . AdminAccess::guardName());

            if (! $dryRun) {
                $legacyRole->forceFill([
                    'name' => $canonicalName,
                    'guard_name' => AdminAccess::guardName(),
                ])->save();
            }
        }
    }

    protected function normalizePermissions(bool $dryRun): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $legacyPermissions = Permission::query()
            ->where('guard_name', '!=', AdminAccess::guardName())
            ->get();

        foreach ($legacyPermissions as $legacyPermission) {
            $existingWebPermission = Permission::query()
                ->where('name', $legacyPermission->name)
                ->where('guard_name', AdminAccess::guardName())
                ->first();

            if ($existingWebPermission) {
                $this->line("Merging permission {$legacyPermission->name} ({$legacyPermission->id}) into web guard");

                if ($dryRun) {
                    continue;
                }

                $this->mergePermissionAssignments($legacyPermission->id, $existingWebPermission->id);
                $legacyPermission->delete();
                continue;
            }

            $this->line("Updating permission {$legacyPermission->name} ({$legacyPermission->id}) to guard " . AdminAccess::guardName());

            if (! $dryRun) {
                $legacyPermission->forceFill([
                    'guard_name' => AdminAccess::guardName(),
                ])->save();
            }
        }
    }

    protected function ensureCanonicalRoles(bool $dryRun): void
    {
        foreach (AdminAccess::canonicalRoleNames() as $roleName) {
            if ($dryRun) {
                $this->line("Ensuring role {$roleName} exists on guard " . AdminAccess::guardName());
                continue;
            }

            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => AdminAccess::guardName(),
            ]);
        }
    }

    protected function ensureCanonicalPermissions(bool $dryRun): void
    {
        foreach (AdminPermissions::all() as $permissionName) {
            if ($dryRun) {
                $this->line("Ensuring permission {$permissionName} exists on guard " . AdminAccess::guardName());
                continue;
            }

            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => AdminAccess::guardName(),
            ]);
        }
    }

    protected function syncCanonicalRolePermissions(bool $dryRun): void
    {
        foreach (AdminPermissions::defaultRolePermissions() as $roleName => $permissionNames) {
            $normalizedRoleName = AdminAccess::normalizeRoleName($roleName);

            if ($dryRun) {
                $this->line('Ensuring role ' . $normalizedRoleName . ' has permissions: ' . implode(', ', $permissionNames));
                continue;
            }

            $role = Role::firstOrCreate([
                'name' => $normalizedRoleName,
                'guard_name' => AdminAccess::guardName(),
            ]);

            $missingPermissions = array_diff(
                $permissionNames,
                $role->permissions()
                    ->where('guard_name', AdminAccess::guardName())
                    ->pluck('name')
                    ->all()
            );

            if ($missingPermissions !== []) {
                $role->givePermissionTo($missingPermissions);
            }
        }
    }

    protected function backfillLegacyAdmins(bool $dryRun): void
    {
        [$sourceTable, $admins] = $this->legacyAdmins();

        if (! $sourceTable || $admins->isEmpty()) {
            return;
        }

        $rolesByAdmin = $this->legacyRolesByAdmin($sourceTable);

        foreach ($admins as $admin) {
            $roleNames = collect($rolesByAdmin[$admin->admin_id] ?? ['Admin'])
                ->map(fn (string $role): string => AdminAccess::normalizeRoleName($role))
                ->filter(fn (string $role): bool => AdminAccess::isAdminRoleName($role))
                ->unique()
                ->values()
                ->all();

            if ($roleNames === []) {
                $roleNames = ['Admin'];
            }

            $this->line("Backfilling legacy admin {$admin->admin_email} from {$sourceTable}");

            if ($dryRun) {
                continue;
            }

            $user = User::where('email', $admin->admin_email)->first();

            if (! $user) {
                $user = new User();
                $user->email = $admin->admin_email;
            }

            if (blank($user->name)) {
                $user->name = $admin->admin_name;
            } else {
                $user->name = $admin->admin_name ?: $user->name;
            }

            if (property_exists($user, 'phone') || Schema::hasColumn($user->getTable(), 'phone')) {
                $user->phone = $admin->admin_phone ?: $user->phone;
            }

            $shouldImportPassword = blank($user->getRawOriginal('password'));

            if ($shouldImportPassword) {
                $user->password = Str::random(40);
            }

            $user->save();

            if ($shouldImportPassword) {
                $this->persistImportedPassword($user, (string) $admin->admin_password);
            }

            $user->syncRoles($roleNames);
        }
    }

    protected function legacyAdmins(): array
    {
        if (Schema::hasTable('tbl_admin')) {
            return ['tbl_admin', DB::table('tbl_admin')->get()];
        }

        if (Schema::hasTable('legacy_admins')) {
            return ['legacy_admins', DB::table('legacy_admins')->get()];
        }

        return [null, collect()];
    }

    protected function legacyRolesByAdmin(?string $sourceTable): array
    {
        if ($sourceTable === 'legacy_admins'
            && Schema::hasTable('legacy_admin_roles')
            && Schema::hasTable('legacy_roles')) {
            return DB::table('legacy_admin_roles')
                ->join('legacy_roles', 'legacy_roles.id_roles', '=', 'legacy_admin_roles.roles_id_roles')
                ->select('legacy_admin_roles.admin_admin_id as admin_id', 'legacy_roles.name')
                ->get()
                ->groupBy('admin_id')
                ->map(fn (Collection $rows): array => $rows->pluck('name')->all())
                ->all();
        }

        if ($sourceTable === 'tbl_admin'
            && Schema::hasTable('tbl_role_user')
            && Schema::hasTable('roles')) {
            return DB::table('tbl_role_user')
                ->join('roles', 'roles.id', '=', 'tbl_role_user.role_id')
                ->select('tbl_role_user.admin_id', 'roles.name')
                ->get()
                ->groupBy('admin_id')
                ->map(fn (Collection $rows): array => $rows->pluck('name')->all())
                ->all();
        }

        return [];
    }

    protected function mergeRoleAssignments(int $legacyRoleId, int $canonicalRoleId): void
    {
        if (Schema::hasTable('model_has_roles')) {
            $assignments = DB::table('model_has_roles')
                ->where('role_id', $legacyRoleId)
                ->get();

            foreach ($assignments as $assignment) {
                DB::table('model_has_roles')->insertOrIgnore([
                    'role_id' => $canonicalRoleId,
                    'model_type' => $assignment->model_type,
                    'model_id' => $assignment->model_id,
                ]);
            }

            DB::table('model_has_roles')->where('role_id', $legacyRoleId)->delete();
        }

        if (Schema::hasTable('role_has_permissions')) {
            $permissions = DB::table('role_has_permissions')
                ->where('role_id', $legacyRoleId)
                ->get();

            foreach ($permissions as $permission) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $permission->permission_id,
                    'role_id' => $canonicalRoleId,
                ]);
            }

            DB::table('role_has_permissions')->where('role_id', $legacyRoleId)->delete();
        }
    }

    protected function mergePermissionAssignments(int $legacyPermissionId, int $canonicalPermissionId): void
    {
        if (Schema::hasTable('role_has_permissions')) {
            $roles = DB::table('role_has_permissions')
                ->where('permission_id', $legacyPermissionId)
                ->get();

            foreach ($roles as $role) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $canonicalPermissionId,
                    'role_id' => $role->role_id,
                ]);
            }

            DB::table('role_has_permissions')->where('permission_id', $legacyPermissionId)->delete();
        }

        if (Schema::hasTable('model_has_permissions')) {
            $assignments = DB::table('model_has_permissions')
                ->where('permission_id', $legacyPermissionId)
                ->get();

            foreach ($assignments as $assignment) {
                DB::table('model_has_permissions')->insertOrIgnore([
                    'permission_id' => $canonicalPermissionId,
                    'model_type' => $assignment->model_type,
                    'model_id' => $assignment->model_id,
                ]);
            }

            DB::table('model_has_permissions')->where('permission_id', $legacyPermissionId)->delete();
        }
    }

    protected function persistImportedPassword(User $user, string $password): void
    {
        if ($password === '') {
            return;
        }

        DB::table($user->getTable())
            ->where($user->getKeyName(), $user->getKey())
            ->update([
                'password' => $this->normalizeImportedPassword($password),
            ]);

        $user->refresh();
    }

    protected function normalizeImportedPassword(string $password): string
    {
        if ($this->isMd5Digest($password) || $this->isPasswordHash($password)) {
            return $password;
        }

        return Hash::make($password);
    }

    protected function isMd5Digest(string $value): bool
    {
        return preg_match('/^[a-f0-9]{32}$/i', $value) === 1;
    }

    protected function isPasswordHash(string $value): bool
    {
        return password_get_info($value)['algo'] !== null;
    }
}
