<?php

namespace App\Contracts;

use App\Models\Location;

interface LocationContract
{
    public function getLocation(string $countryName): Location;
}
