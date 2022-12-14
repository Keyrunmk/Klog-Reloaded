<?php

namespace App\Repositories;

use App\Contracts\ProfileContract;
use App\Models\Profile;
use Exception;

class ProfileRepository extends BaseRepository implements ProfileContract
{
    public function __construct(Profile $model)
    {
        parent::__construct($model);
    }

    public function updateProfile(Profile $profile, array $attributes): void
    {
        $profile->update($attributes);
    }
}
