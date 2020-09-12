<?php

namespace App\Listeners;

use App\Events\LabMembershipChange;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Auth;
use Activity;

class LabMembershipChanged
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
     * @param  LabMembershipChange  $event
     * @return void
     */
    public function handle(LabMembershipChange $event)
    {
        $msg = '[LabMembershipLogger] => ' .  Auth::user()->name . ' hat das Labor "' . $event->lab->name . '" in Gruppe ' . $event->lab->membership . ' verschoben.';
            Log::info($msg);
//            Activity::log($msg);

    }
}
