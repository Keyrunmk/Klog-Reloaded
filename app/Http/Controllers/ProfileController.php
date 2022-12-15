<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Services\ProfileService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileController extends BaseController
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show(int $profile_id): JsonResponse
    {
        try {
            $profile = $this->profileService->find($profile_id);
            return $this->successResponse(message: "Profile id: $profile_id", data: $this->resource($profile));
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function update(int $profile_id, ProfileRequest $request): JsonResponse
    {
        try {
            $profile = $this->profileService->find($profile_id);
            $this->authorize("update", $profile);
            $profile = $this->profileService->update($profile, $request->all());
            return $this->successResponse(message: "Profile updated", data: $this->resource($profile));
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse("No profile with id: $profile_id", (int) $exception->getCode());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function follow(int $profile_id): JsonResponse
    {
        try {
            $response = $this->profileService->followProfile($profile_id);
            return $this->successResponse($response);
        } catch (NotFoundException $exception) {
            return $this->errorResponse("Couldn't find the profile to follow", (int) $exception->getCode());
        } catch (Exception $exception) {
            return $this->errorResponse("Something went wrong", (int)$exception->getCode());
        }
    }

    public function resource(Profile $data): JsonResource
    {
        return new ProfileResource($data);
    }
}
