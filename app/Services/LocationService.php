<?php

namespace App\Services;

class LocationService
{
    public function getCountryName(): string
    {
        $user_ip = getenv('REMOTE_ADDR');
        $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
        $country = $geo["geoplugin_countryName"] ?? "world";

        return $country;
    }
}
