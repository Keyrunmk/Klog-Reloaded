<?php

namespace App\Traits;

use App\Models\Role;

trait HasRolesAndPermissions
{
    public Role $roles;

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->role->slug === $role;
    }

    public function hasPermissionThroughRole(string $permissionSlug): bool
    {
        $permissions = $this->role->permissions;
        foreach ($permissions as $permission) {
            if ($permission->slug === $permissionSlug) {
                return true;
            }
        }

        return false;
    }
}
