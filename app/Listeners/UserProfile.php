<?php

namespace App\Listeners;

use App\Contracts\ProfileContract;
use App\Repositories\ProfileRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserProfile
{
    protected ProfileRepository $profileRepsoitory;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ProfileContract $profileRepsoitory)
    {
        $this->profileRepsoitory = $profileRepsoitory;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->profileRepsoitory->create([
            "title" => $event->user->username,
            "user_id" => $event->user->id,
        ]);
    }
}
