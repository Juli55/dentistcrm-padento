<?php

namespace App\Listeners;

use App\Events\SystemSettingChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Activity;

class SystemLogger
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
     * @param  SystemSettingChanged  $event
     * @return void
     */
    public function handle(SystemSettingChanged $event)
    {
//        Activity::log($event->msg);
    }
}
