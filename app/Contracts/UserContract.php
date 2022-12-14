<?php

namespace App\Contracts;

use App\Enum\UserStatusEnum;
use App\Models\User;
use App\Models\UserVerification;

interface UserContract
{
    public function setLocation(User $user): void;

    public function findWhere(string $email, UserStatusEnum $status): mixed;

    public function getUserForActivation(string $token, int $user_id): UserVerification;

    public function verifyUser(User $user): void;
}
