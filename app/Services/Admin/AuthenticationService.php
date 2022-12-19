<?php

namespace App\Services\Admin;

use App\Contracts\AdminContract;
use App\Models\Admin;
use App\Models\Role;
use App\Repositories\AdminRepository;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    public AdminRepository $adminRepository;

    public function __construct(AdminContract $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function register(array $attributes): Admin
    {
        $attributes["password"] = Hash::make($attributes["password"]);
        $role = Cache::remember("role-admin", 86400, function () {
            return Role::where("slug", "admin")->first();
        });
        $attributes = array_merge($attributes, [
            "role_id" => $role->id,
        ]);

        $admin = $this->adminRepository->create($attributes);

        return $admin;
    }

    public function login(array $attributes): array
    {
        $token =  Auth::guard("admin-api")->attempt($attributes);

        if (empty($token)) {
            throw new Exception("Invalid Credentials", Response::HTTP_UNAUTHORIZED);
        }

        return [
            "token" => $token,
            "expires_in" => auth()->guard("admin-api")->factory()->getTTL() . " seconds",
        ];
    }

    public function logout(): void
    {
        Auth::guard("admin-api")->logout();
    }

    public function delete(int $admin_id): void
    {
        $this->adminRepository->delete($admin_id);
    }
}
