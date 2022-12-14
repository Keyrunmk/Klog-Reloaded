<?php

namespace App\Providers;

use App\Models\Permission;
use Exception;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            // Permission::get()->map(function ($permission) {
            //     Gate::define($permission->slug,)
            // });
        } catch (Exception $e) {
            // $e;
            return false;
        }
    }
}
