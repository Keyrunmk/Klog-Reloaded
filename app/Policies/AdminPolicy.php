<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;
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
    public function create(Admin $user): Response|bool
    {
        return auth()->guard("admin-api")->user()->role->slug === "owner";
    }

    public function createCategory(Admin $admin): bool
    {
        return auth()->guard("admin-api")->user()->hasPermissionThroughRole("create-category");
    }
}
