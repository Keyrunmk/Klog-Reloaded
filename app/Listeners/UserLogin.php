<?php

namespace App\Listeners;

use App\Http\Resources\BaseResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class UserLogin
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
        try {
            $token = Auth::login($event->user);
        } catch (JWTException $e) {
            throw $e;
        }

        return new UserResource($event->user, $token);
    }
}
