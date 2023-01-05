<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Grants\CustomGrant;
use App\Grants\OtpVerify;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use App\Policies\PostPolicy;
use App\Policies\ProfilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Profile::class => ProfilePolicy::class,
        Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define("delete-post-comment", function (User $user, Post $post, Comment $comment) {
            return $user->can("delete", $post) || $user->can("delete", $comment);
        });

        Gate::define("update-post", function (User $user, Post $post) {
            return $user->can("update", $post);
        });

        Passport::tokensExpireIn(now()->addHour(6));

        app(AuthorizationServer::class)->enableGrantType(
            $this->makeOtpGrant(), Passport::tokensExpireIn()
        );
    }

    protected function makeOtpGrant(): CustomGrant
    {
        $grant = new CustomGrant(
            $this->app->make(OtpVerify::class),
        );

        return $grant;
    }
}
