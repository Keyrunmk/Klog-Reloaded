<?php

namespace App\Services;

use App\Contracts\LocationContract;
use App\Models\Location;
use App\Repositories\LocationRepository;
use Exception;

class LocationService
{
    protected LocationRepository $locationRepository;

    public function __construct(LocationContract $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function getCountry(): Location
    {
        $user_ip = getenv('REMOTE_ADDR');
        $country = "World";
        if ($user_ip) {
            $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip")) ?? "World";
            $country = $geo["geoplugin_countryName"];
        }

        return $this->locationRepository->getLocation($country);
    }
}
