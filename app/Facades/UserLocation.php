<?php

namespace App\facades;

use App\Services\LocationService;

class UserLocation
{
    public static function resolveFacade($class): object
    {
        return app()[$class];
    }

    public static function __callStatic($method, $arguments): string
    {
        return self::resolveFacade(LocationService::class)->$method(...$arguments);
    }
}
