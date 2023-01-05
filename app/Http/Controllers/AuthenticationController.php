<?php

namespace App\Http\Controllers;

use App\Enum\UserSourceEnum;
use App\Events\UserRegisteredEvent;
use App\Events\VerifyUserEvent;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\TokenRequest;
use App\Http\Resources\UserResource;
use App\Services\Oauth\AuthService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthenticationController extends BaseController
{
    protected UserService $userService;
    protected AuthService $authService;

    public function __construct(UserService $userService, AuthService $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
        $this->middleware("guest:api")->only(["login", "register", "verify", "verifyOtp"]);
        $this->middleware("auth:api")->only(["logout", "refreshToken"]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $attributes = array_merge($request->all(), [
                "source" => UserSourceEnum::Local,
            ]);
            $user = $this->userService->register($attributes);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception);
        }
        DB::commit();

        UserRegisteredEvent::dispatch($user);

        return $this->successResponse("Welcome", new UserResource("Check your mail to verify account", $user));
    }

    public function verify(int $user_id, TokenRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $this->userService->verify($user_id, $request->all());
            VerifyUserEvent::dispatch($data["user"]);
        } catch (Exception $exception) {
            DB::rollBack();
            try {
                $this->userService->retry($user_id);
            } catch (Exception $exception) {
                return $this->handleException($exception);
            }
            return $this->handleException($exception);
        }
        DB::commit();

        return $this->successResponse($data["message"]);
    }

    public function verifyOtp(Request $request)
    {
        try {
            $data = $this->authService->verifyOtp($request);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $data;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $token = $this->authService->login($request->all());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse($token);
    }

    public function logout(): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->authService->logout();
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception);
        }
        DB::commit();

        return $this->successResponse("Logged out successfully");
    }
}
