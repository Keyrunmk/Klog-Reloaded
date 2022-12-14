<?php

namespace App\Http\Controllers;

use App\Events\UserRegisteredEvent;
use App\Events\VerifyUserEvent;
use App\Services\UserService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function register(Request $request): mixed
    {
        try {
            $user = $this->userService->register($request);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return UserRegisteredEvent::dispatch($user);
    }

    public function verify(int $user_id, Request $request): JsonResponse
    {
        try {
            $data = $this->userService->verify($user_id, $request);
            VerifyUserEvent::dispatch($data["user"]);
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse("Wrong token");
        } catch (Exception $exception) {
            $this->userService->retry($user_id);
            return $this->handleException($exception);
        }

        return $this->successResponse($data["message"]);
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $token = $this->userService->login($request);
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse("No user with given email address", (int) $exception->getCode());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        // Cache::flush();
        return $this->successResponse($token);
    }

    public function logout(): JsonResponse
    {
        try {
            $this->userService->logout();
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse("Logged out successfully");
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
