<?php

namespace App\Services;

use App\Contracts\ProfileContract;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Repositories\ProfileRepository;
use App\Traits\ImageTrait;
use Intervention\Image\Facades\Image;

class ProfileService
{
    use ImageTrait;

    protected ProfileRequest $profileValidate;
    protected ProfileRepository $profileRepository;

    public function __construct(ProfileContract $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function find(int $profile_id): Profile
    {
        return $this->profileRepository->findOneOrFail($profile_id);
    }

    public function update(Profile $profile, array $attributes): Profile
    {
        $this->deleteProfileImage($profile);

        if ($attributes["image"] ?? false) {
            unset($attributes["image"]);
            $imagePath = $this->getImagePath();
            if (!$profile->image()->update(["path" => $imagePath])) {
                $profile->image()->create(["path" => $imagePath]);
            }
        }
        $this->profileRepository->updateProfile($profile, $attributes);

        return $profile;
    }

    public function followProfile(int $profile_id): string
    {
        $response = auth()->user()->following()->toggle($profile_id);

        if (!empty($response["attached"])) {
            return "Following profile id " . $response["attached"][0];
        }

        return "Unfollowed profile id " . $response["detached"][0];
    }

    public function deleteProfileImage(Profile $profile): void
    {
        $this->deleteImage($profile);
    }
}
