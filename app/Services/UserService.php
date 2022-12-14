<?php

namespace App\Services;

use App\Contracts\UserContract;
use App\Models\Role;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Validations\ValidateLoginRequest;
use App\Validations\ValidateRegisterRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public UserRepository $userRepository;
    public ValidateRegisterRequest $validateRegister;
    public ValidateLoginRequest $validateLogin;

    public function __construct(UserContract $userRepository, ValidateRegisterRequest $validateRegister, ValidateLoginRequest $validateLogin)
    {
        $this->userRepository = $userRepository;
        $this->validateRegister = $validateRegister;
        $this->validateLogin = $validateLogin;
    }

    public function register(Request $request): User
    {
        $attributes = $this->validateRegister->validate($request);
        $attributes["password"] = Hash::make($attributes["password"]);
        $role_id = Cache::remember("role_user", 86400, function () {
            return Role::where("slug", "user")->value("id");
        });

        $attributes = array_merge($attributes, [
            "role_id" => $role_id,
        ]);

        $user = $this->userRepository->create($attributes);
        $this->userRepository->setLocation($user);

        return $user;
    }

    public function verify(int $user_id, Request $request): array
    {
        $request = $request->validate([
            "token" => ["required", "string"],
        ]);

        $userVerify = $this->userRepository->getUserForActivation($request["token"], $user_id);
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

    public function login(Request $request): string
    {
        $attributes = $this->validateLogin->validate($request);

        $token = Auth::attempt($attributes);
        if (!$token) {
            throw new Exception("Invalid Credentials", Response::HTTP_BAD_REQUEST);
        };

        return $token;
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
