<?php

namespace App\Services;

use App\Contracts\UserContract;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Oauth\AuthService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    protected UserRepository $userRepository;
    protected AuthService $authService;

    public function __construct(UserContract $userRepository, AuthService $authService)
    {
        $this->userRepository = $userRepository;
        $this->authService = $authService;
    }

    public function register(array $attributes): User
    {
        $attributes["password"] = Hash::make($attributes["password"]);
        $location_id = $this->getLocation()->id;
        $role_id = Cache::remember("role_user", 86400, function () {
            return $this->getRoleId("user");
        });

        $attributes = array_merge($attributes, [
            "role_id" => $role_id,
            "location_id" => $location_id,
        ]);

        $user = $this->userRepository->create($attributes);

        return $user;
    }

    public function verify(int $user_id, array $attributes): array
    {
        $userVerify = $this->userRepository->getUserForActivation($attributes["token"], $user_id);
        $user = $userVerify->user;
        $message = "Your email is already verified.";
        if (!$user->email_verified_at) {
            $this->userRepository->verifyUser($user);
            $message = "Your email is now verified.";
        }
        $this->userRepository->deleteUserVerificationCode($userVerify);

        return [
            "message" => $message,
            "user" => $user,
        ];
    }

    public function retry(int $user_id): void
    {
        $this->userRepository->deleteInactiveUser($user_id);
    }

    // public function refreshToken(): array
    // {
    //     return [
    //         "token" => Auth::refresh(),
    //     ];
    // }
}
