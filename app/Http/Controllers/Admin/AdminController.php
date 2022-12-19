<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AdminRegisterRequest;
use App\Http\Resources\AdminResource;
use App\Services\Admin\AuthenticationService;
use Exception;
use Illuminate\Http\JsonResponse;

class AdminController extends BaseController
{
    protected AuthenticationService $authenticationService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
        $this->middleware("adminRole:create")->only(["register", "destroy"]);
    }

    public function register(AdminRegisterRequest $request): JsonResponse
    {
        try {
            $admin = $this->authenticationService->register($request->all());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Admin created", data: new AdminResource($admin));
    }

    public function login(AdminLoginRequest $request): JsonResponse
    {
        try {
            $token = $this->authenticationService->login($request->all());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse($token);
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authenticationService->logout();
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse("Logged out successfully");
    }

    public function destroy(int $admin_id): JsonResponse
    {
        try {
            $this->authenticationService->delete($admin_id);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse("Admin id: $admin_id deleted successfully");
    }
}
