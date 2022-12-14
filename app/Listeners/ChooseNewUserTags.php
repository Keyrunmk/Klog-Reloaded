<?php

namespace App\Listeners;

use App\Http\Resources\BaseResource;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ChooseNewUserTags
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
        return new BaseResource([
            "redirect" => "Redirect to choose tags",
        ]);
    }
}
