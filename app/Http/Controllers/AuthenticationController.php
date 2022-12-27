<?php

namespace App\Http\Controllers;

use App\Events\UserRegisteredEvent;
use App\Events\VerifyUserEvent;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\TokenRequest;
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
        $this->middleware("guest:api")->only(["login", "register", "verify"]);
        $this->middleware("auth:api")->only(["logout", "refreshToken"]);
    }

    public function register(RegisterRequest $request): mixed
    {
        DB::beginTransaction();
        try {
            $user = $this->userService->register($request->all());
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception);
        }
        DB::commit();

        return UserRegisteredEvent::dispatch($user);
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
        } catch(Exception $exception) {
            return $this->handleException($exception);
        }

        return $data;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $token = $this->authService->login($request->all());
            // Cache::flush();
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse($token);
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse("Logged out successfully");
    }

    // public function refreshToken(): JsonResponse
    // {
    //     try {
    //         $token = $this->userService->refreshToken();
    //     } catch (Exception $exception) {
    //         return $this->handleException($exception);
    //     }

    //     return $this->successResponse($token);
    // }
}
