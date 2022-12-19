<?php

namespace App\Repositories;

use App\Contracts\LocationContract;
use App\Models\Location;

class LocationRepository extends BaseRepository implements LocationContract
{
    public function __construct(Location $model)
    {
        parent::__construct($model);
    }

    public function getLocation(string $countryName): Location
    {
        $location = $this->findBy(["country_name" => $countryName]);
        if (!$location) {
            $location = $this->create(["country_name" => $countryName]);
        }
        return $location;
    }
}