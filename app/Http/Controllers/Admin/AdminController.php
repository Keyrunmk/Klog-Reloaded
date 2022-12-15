<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AdminRegisterRequest;
use App\Http\Resources\AdminResource;
use App\Services\Admin\AuthenticationService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            $data = $this->authenticationService->register($request->all());
            return $this->successResponse(message: "Admin created", data: new AdminResource($data["admin"], $data["token"]));
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function login(AdminLoginRequest $request): JsonResponse
    {
        try {
            $token = $this->authenticationService->login($request->all());
            return $this->successResponse($token);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authenticationService->logout();
            return $this->successResponse("Logged out successfully");
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function destroy(int $admin_id): JsonResponse
    {
        try {
            $this->authenticationService->delete($admin_id);
            return $this->successResponse("Admin id: $admin_id deleted successfully");
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse("Failed to find the admin with id: $admin_id", (int) $exception->getCode());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }
}
