<?php

namespace App\Services\Admin;

use App\Contracts\AdminContract;
use App\Models\Role;
use App\Repositories\AdminRepository;
use App\Validations\AdminLogin;
use App\Validations\AdminRegister;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
class AuthenticationService
{
    public AdminRepository $adminRepository;
    public AdminRegister $validateRegister;
    public AdminLogin $validateLogin;

    public function __construct(AdminContract $adminRepository, AdminRegister $validateRegister, AdminLogin $validateLogin)
    {
        $this->adminRepository = $adminRepository;
        $this->validateRegister = $validateRegister;
        $this->validateLogin = $validateLogin;
    }

    public function register(Request $request): array
    {
        $attributes = $this->validateRegister->validate($request);
        $attributes["password"] = Hash::make($attributes["password"]);

        $role = Cache::remember("role-admin", 86400, function () {
            return Role::where("slug", "admin")->first();
        });

        $attributes = array_merge($attributes, [
            "role_id" => $role->id,
        ]);

        $admin = $this->adminRepository->create($attributes);
        $token = Auth::guard("admin-api")->login($admin);
        if (empty($token)) {
            throw new Exception("Failed to login", Response::HTTP_UNAUTHORIZED);
        }

        return [
            "admin" => $admin,
            "token" => $token,
        ];
    }

    public function login(Request $request): string
    {
        $attributes = $this->validateLogin->validate($request);

        $token =  Auth::guard("admin-api")->attempt($attributes);

        if (empty($token)) {
            throw new Exception("Invalid Credentials", Response::HTTP_UNAUTHORIZED);
        }

        return $token;
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
