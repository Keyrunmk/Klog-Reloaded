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
        try {
            $user_ip = getenv('REMOTE_ADDR');
            $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
            $country = $geo["geoplugin_countryName"];
        } catch (Exception $exception) {
            $country = "World";
        }

        return $this->locationRepository->getLocation($country);
    }
}
