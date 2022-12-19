<?php

namespace App\Services;

use App\facades\UserLocation;
use App\Models\Location;
use App\Models\Role;

class BaseService
{
    public function getLocation(): Location
    {
        return UserLocation::getCountry();
    }

    public function getRoleId(string $slug): int
    {
        return Role::where("slug", $slug)->value("id");
    }
}