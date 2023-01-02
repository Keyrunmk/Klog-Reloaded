<?php

namespace App\Facades;

use App\Models\Location;
use App\Services\LocationService;

class UserLocation
{
    public static function resolveFacade($class): object
    {
        return app()[$class];
    }

    public static function __callStatic($method, $arguments): Location
    {
        return self::resolveFacade(LocationService::class)->$method(...$arguments);
    }
}
