<?php

namespace App\Listeners;

use App\Events\MailError;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Mail;

class MailErrorListener
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
     * @param  MailError  $event
     * @return void
     */
    public function handle(MailError $event)
    {
        $patient = $event->patient;
        $msg = '[MailError] => Mail an "' . $patient['name'] . '" <' . $patient['email'] . '> [PLZ: ' . $patient['zip'] . '] konnte nicht verschickt werden.';
        Log::info($msg);
//        Activity::log($msg);
    }
}
