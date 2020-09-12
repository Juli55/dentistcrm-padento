<?php

namespace App\Listeners;

use App\Events\NoLabWasFound;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Activity;
use Mail;

class NoLabFound
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
     * @param  NoLabWasFound  $event
     * @return void
     */
    public function handle(NoLabWasFound $event)
    {
        $msg = '[MissingLabLogger] => "' . $event->name . '" [Tel: ' . $event->tel . '] [PLZ: ' . $event->plz . '] konnte keinem Labor zugeordent werden';
//        Activity::log($msg);
        // Mail::send( 'emails.system-mail', ['msg' => $msg], function($message)
        // {
        //     $message->from('info@padento.de', 'Padento');
        //     $message->getHeaders()->addTextHeader('X-MC-Tags', 'Fehler, Systemmail, Kein Labor');
        //     $message->to(env('DEV_MAIL','info@padento.de'), 'Marc')->subject('[Padento] Kein Labor gefunden!');
        // });
    }
}
