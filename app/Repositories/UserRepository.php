<?php

namespace App\Repositories;

use App\Contracts\UserContract;
use App\Enum\UserStatusEnum;
use App\facades\UserLocation;
use App\Models\User;
use App\Models\UserVerification;

class UserRepository extends BaseRepository implements UserContract
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findWhere(string $email, UserStatusEnum $status): mixed
    {
        return $this->model->where("email", $email)->where("status", $status)->first();
    }

    public function getUserForActivation(string $token, int $user_id): UserVerification
    {
        $userVerification =  UserVerification::where("token", $token)->where("user_id", $user_id)->with("user")->firstOrFail();
        return $userVerification;
    }

    public function deleteUserVerificationCode(UserVerification $userVerification): void
    {
        $userVerification->delete();
    }

    public function verifyUser(User $user): void
    {
        $user->email_verified_at = now();
        $user->status = UserStatusEnum::Active;
        $user->save();
    }

    public function deleteInactiveUser(int $user_id): void
    {
        $this->model->findOrFail($user_id)->where("status", "inactive")->delete();
    }
}
