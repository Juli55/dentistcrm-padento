<?php

namespace App\Listeners;

use App\Events\TimeframesMissing;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Activity;

class MissingTimeframes
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
     * @param  TimeframesMissing  $event
     * @return void
     */
    public function handle(TimeframesMissing $event)
    {
        $lab = $event->lab;

        $msg = '[TimeFrameLogger] => Für das Labor "' . $lab->name . '" wurden noch keine Zeiten festgelegt.';
//        Activity::log($msg);

    }
}
