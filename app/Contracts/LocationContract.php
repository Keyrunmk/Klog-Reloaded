<?php

namespace App\Contracts;

interface LocationContract
{
    public function getLocationId(string $countryName): int;
}
