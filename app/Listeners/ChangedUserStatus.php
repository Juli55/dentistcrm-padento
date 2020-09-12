<?php

namespace App\Listeners;

use App\Events\UserStatusChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Activity;

class ChangedUserStatus
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
     * @param  UserStatusChanged  $event
     * @return void
     */
    public function handle(UserStatusChanged $event)
    {
//        Activity::log($event->msg);
    }
}
