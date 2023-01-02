<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class BasePolicy
{
    use HandlesAuthorization;

    protected string $policyName;
    protected string $modelName;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->policyName = class_basename($this);
        // $this->modelName = str_replace("Policy", "", $this->policyName);
        // dd($this->modelName);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionThroughRole("user-access");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Model $model): bool
    {
        return $user->id == $model->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Model $model): bool
    {
        return $user->id === $model->user_id;
    }
}
