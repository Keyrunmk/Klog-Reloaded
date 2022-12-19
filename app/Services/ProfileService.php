<?php

namespace App\Services;

use App\Contracts\ProfileContract;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Repositories\ProfileRepository;
use Intervention\Image\Facades\Image;

class ProfileService
{
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

    public function update(Profile $profile, array $request): Profile
    {
        $this->profileRepository->updateProfile($profile, $request);

        if (request("image")) {
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
        $response = auth()->user()->following()->toggle($profile_id);

        if (!empty($response["attached"])) {
            return "Following profile id " . $response["attached"][0];
        }

        return "Unfollowed profile id " . $response["detached"][0];
    }
}
