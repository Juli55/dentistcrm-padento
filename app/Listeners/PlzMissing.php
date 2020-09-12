<?php

namespace App\Listeners;

use App\Events\PlzIsMissing;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Activity;
use Mail;

class PlzMissing
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
     * @param  PlzIsMissing  $event
     * @return void
     */
    public function handle(PlzIsMissing $event)
    {
        $msg = '[MissingPLZLogger] => ' . $event->plz . ' fehlt in plz.txt';
//        Activity::log($msg);
        // Mail::send( 'emails.system-mail', ['msg' => $msg], function($message)
        // {
        //     $message->from('info@padento.de', 'Padento');
        //     $message->getHeaders()->addTextHeader('X-MC-Tags', 'Fehler, Systemmail, PLZ fehlt');
        //     $message->to('buero@padento.de', 'Marina Ehrich')->subject('[Padento] PLZ fehlt!');
        // });
    }
}
