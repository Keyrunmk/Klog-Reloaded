<?php

namespace App\Http\Controllers\Oauth;

use App\Enum\UserSourceEnum;
use App\Enum\UserStatusEnum;
use App\Events\UserRegisteredEvent;
use App\Http\Controllers\BaseController;
use App\Http\Resources\UserResource;
use App\Services\Oauth\ForeignServerService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OauthController extends BaseController
{
    protected ForeignServerService $foreignServerService;
    protected UserService $userService;

    public function __construct(ForeignServerService $foreignServerService, UserService $userService)
    {
        $this->foreignServerService = $foreignServerService;
        $this->userService = $userService;
    }

    public function redirect(): RedirectResponse
    {
        return $this->foreignServerService->redirectToForeignOauthServer();
    }

    public function registerOutSourceUser(Request $request): JsonResponse
    {
        try {
            $response = $this->foreignServerService->getAccessToken($request);
            $user = $this->foreignServerService->getUserFromRedirectClient($response);
            //we can prompt user to create password but for the time
            $attributes = array_merge($user, [
                "password" => "password",
                "username" => $user["first_name"],
                "source" => UserSourceEnum::Foreign,
                "status" => UserStatusEnum::Active,
            ]);

            $registerUserResponse = $this->userService->register($attributes);

            UserRegisteredEvent::dispatch($registerUserResponse);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse("Yo welcome", new UserResource("Registered", $registerUserResponse));
    }
}