<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Resources\AdminResource;
use App\Services\Admin\AuthenticationService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends BaseController
{
    protected AuthenticationService $authenticationService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
        $this->middleware("adminRole:create")->only(["register", "destroy"]);
    }

    public function register(Request $request): JsonResponse
    {
        try {
            $data = $this->authenticationService->register($request);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Admin created", data: new AdminResource($data["admin"], $data["token"]));
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $token = $this->authenticationService->login($request);
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
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse("Failed to find the admin with id: $admin_id", (int) $exception->getCode());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse("Admin id: $admin_id deleted successfully");
    }
}
