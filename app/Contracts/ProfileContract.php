<?php

namespace App\Contracts;

use App\Models\Profile;

interface ProfileContract
{
    public function updateProfile(Profile $profile, array $attributes): void;
}
