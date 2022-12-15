<?php

namespace App\Http\Controllers;

use App\Events\UserRegisteredEvent;
use App\Events\VerifyUserEvent;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\TokenRequest;
use App\Services\UserService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AuthenticationController extends BaseController
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware("guest:api")->only(["login", "register", "verify"]);
        $this->middleware("auth:api")->only(["logout", "refreshToken"]);
    }

    public function register(RegisterRequest $request): mixed
    {
        try {
            $user = $this->userService->register($request->all());
            return UserRegisteredEvent::dispatch($user);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function verify(int $user_id, TokenRequest $request): JsonResponse
    {
        try {
            $data = $this->userService->verify($user_id, $request->all());
            VerifyUserEvent::dispatch($data["user"]);
            return $this->successResponse($data["message"]);
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse("Wrong token");
        } catch (Exception $exception) {
            $this->userService->retry($user_id);
            return $this->handleException($exception);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $token = $this->userService->login($request->all());
            // Cache::flush();
            return $this->successResponse($token);
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse("No user with given email address", (int) $exception->getCode());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->userService->logout();
            return $this->successResponse("Logged out successfully");
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function refreshToken(): JsonResponse
    {
        try {
            return $this->successResponse($this->userService->refreshToken());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }
}
