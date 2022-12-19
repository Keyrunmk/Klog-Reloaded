<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AdminPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Admin $admin): Response|bool
    {
        return $admin->role->slug === "owner";
    }

    public function createCategory(Admin $admin): bool
    {
        return $admin->hasPermissionThroughRole("create-category");
    }
}
