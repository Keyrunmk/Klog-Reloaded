<?php

namespace App\Listeners;

use App\Enum\UserSourceEnum;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailVerification implements ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'register';

    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    public $afterCommit = true;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $token = Str::random(6);
        UserVerification::create([
            "user_id" => $event->user->id,
            "token" => $token,
        ]);

        Mail::to($event->user->email)->send(new VerifyEmail($token));
    }

    /**
     * Determine whether the listener should be queued.
     */
    public function shouldQueue($event)
    {
        return $event->user->source == UserSourceEnum::Local ? true : false;
    }
}
