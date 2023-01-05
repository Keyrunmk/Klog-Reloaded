<?php

namespace App\Listeners;

use App\Enum\UserSourceEnum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBlogAgreement implements ShouldQueue
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
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Mail::raw("Registered in Klog " . now(), function (Message $message) {
            $message->to("example@gmail.com")
                ->from("klog@gmail.com");
        });
    }

    /**
     * Determine whether the listener should be queued.
     */
    public function shouldQueue($event)
    {
        return $event->user->source == UserSourceEnum::Foreign ? true : false;
    }
}
