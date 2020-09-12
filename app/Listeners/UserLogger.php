<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Auth;
use Activity;

class UserLogger
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
     * @param  UserLoggedIn  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $msg = '[UserLogger] => ' . $event->user->name . ' <' . $event->user->email . '> hat sich angemeldet';
        Log::info($msg);
//        Activity::log($msg);
    }
}
