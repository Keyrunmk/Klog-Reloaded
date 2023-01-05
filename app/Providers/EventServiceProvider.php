<?php

namespace App\Providers;

use App\Events\UserLoggedInEvent;
use App\Events\UserRegisteredEvent;
use App\Events\VerifyUserEvent;
use App\Listeners\ChooseNewUserTags;
use App\Listeners\EmailVerification;
use App\Listeners\SendBlogAgreement;
use App\Listeners\SendWelcomeEmail;
use App\Listeners\UserLoggedIn;
use App\Listeners\UserLogin;
use App\Listeners\UserProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserRegisteredEvent::class => [
            EmailVerification::class,
            SendBlogAgreement::class,
        ],
        VerifyUserEvent::class => [
            // UserLogin::class,
            UserProfile::class,
            SendWelcomeEmail::class,
            // ChooseNewUserTags::class,
        ],
        UserLoggedInEvent::class => [
            UserLoggedIn::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
