<?php

namespace App\Providers;

use App\Http\Resources\BaseResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        BaseResource::withoutWrapping();

        DB::listen(function ($query) {
            File::append(
                storage_path("/logs/query.log"),
                $query->sql . " [" . implode(", ", $query->bindings) . "]" . PHP_EOL
            );
        });
    }
}
