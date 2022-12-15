<?php

namespace App\Providers;

use App\Contracts\AdminContract;
use App\Contracts\CategoryContract;
use App\Contracts\LocationContract;
use App\Contracts\PostContract;
use App\Contracts\ProfileContract;
use App\Contracts\UserContract;
use App\Repositories\AdminRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\LocationRepository;
use App\Repositories\PostRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public $repositories = [
        LocationContract::class => LocationRepository::class,
        AdminContract::class => AdminRepository::class,
        UserContract::class => UserRepository::class,
        ProfileContract::class => ProfileRepository::class,
        PostContract::class => PostRepository::class,
        CategoryContract::class => CategoryRepository::class,
    ];

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
        foreach ($this->repositories as $interface => $repository) {
            $this->app->singleton($interface, $repository);
        }
    }
}
