<?php

namespace App\Policies;

use App\Models\User;
use App\Services\Admin\RoleService;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    // public RoleService $roleService;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    // public function __construct(RoleService $roleService)
    // {
    //     $this->roleService = $roleService;
    // }

    public function create(User $user): bool
    {
        return $user->hasRole(["page-admin","page-manager"]);
    }
}
