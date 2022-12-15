<?php

namespace App\Services;

use App\Contracts\LocationContract;
use App\Contracts\UserContract;
use App\facades\UserLocation;
use App\Models\Role;
use App\Models\User;
use App\Repositories\LocationRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public UserRepository $userRepository;
    public LocationRepository $locationRepository;

    public function __construct(UserContract $userRepository, LocationContract $locationRepository)
    {
        $this->userRepository = $userRepository;
        $this->locationRepository = $locationRepository;
    }

    public function register(array $attributes): User
    {
        $attributes["password"] = Hash::make($attributes["password"]);
        $location = UserLocation::getCountryName();
        $location_id = $this->locationRepository->getLocationId($location);
        $role_id = Cache::remember("role_user", 86400, function () {
            return Role::where("slug", "user")->value("id");
        });

        $attributes = array_merge($attributes, [
            "role_id" => $role_id,
            "location_id" => $location_id,
        ]);

        $user = $this->userRepository->create($attributes);
        $this->userRepository->setLocation($user);

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
        $this->userRepository->delete($user_id);
    }

    public function login(array $attributes): array
    {
        $token = Auth::attempt($attributes);
        if (!$token) {
            throw new Exception("Invalid Credentials", Response::HTTP_BAD_REQUEST);
        };

        return ["token" => $token];
    }

    public function logout(): void
    {
        Auth::guard("api")->logout();
    }

    public function refreshToken(): string
    {
        return Auth::refresh();
    }
}
