<?php

namespace App\Listeners;

use App\Jobs\LoggedInThroughPassportEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserLoggedIn
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        LoggedInThroughPassportEmailJob::dispatch($event->user)->onQueue("login");
    }
}
