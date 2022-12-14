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

    public function setLocation(User $user): void
    {
        $userLocation = UserLocation::getCountryName();
        $user->location()->create(["country_name" => $userLocation]);
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
}
