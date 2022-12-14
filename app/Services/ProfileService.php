<?php

namespace App\Services;

use App\Contracts\ProfileContract;
use App\Exceptions\NotFoundException;
use App\Models\Profile;
use App\Repositories\ProfileRepository;
use App\Validations\ValidateProfileRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ProfileService
{
    protected ValidateProfileRequest $profileValidate;
    protected ProfileRepository $profileRepository;

    public function __construct(ValidateProfileRequest $profileValidate, ProfileContract $profileRepository)
    {
        $this->profileValidate = $profileValidate;
        $this->profileRepository = $profileRepository;
    }

    public function find(int $profile_id): Profile
    {
        return $this->profileRepository->findOneOrFail($profile_id);
    }

    public function update(Profile $profile, Request $request): Profile
    {
        $attributes = $this->profileValidate->validate($request);

        $this->profileRepository->updateProfile($profile, $attributes);

        if ($request->image) {
            $imagePath = request("image")->store("uploads", "public");
            $image = Image::make(public_path("storage/$imagePath"))->fit(2000, 2000);
            $image->save();
        }

        if ($imagePath ?? false) {
            if (!$profile->image()->update(["path" => $imagePath])) {
                $profile->image()->create(["path" => $imagePath]);
            }
        }

        return $profile;
    }

    public function followProfile(int $profile_id): string
    {
        $profile = $this->profileRepository->findOneOrFail($profile_id);
        $user = $profile->user;
        //todo - test for profile
        $response = auth()->user()->following()->toggle($user);

        if (!empty($response["attached"])) {
            return "Following profile id " . $response["attached"][0];
        }

        return "Unfollowed profile id " . $response["detached"][0];
    }
}
