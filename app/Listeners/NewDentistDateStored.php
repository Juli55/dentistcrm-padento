<?php

namespace App\Listeners;

use App;
use App\Events\NewDentistDate;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class NewDentistDateStored
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewDentistDate $event
     * @return void
     */
    public function handle(NewDentistDate $event)
    {
        $date    = $event->date;
        $dentist = $date->dentist_contact;
        $lab     = $date->lab;
        $user    = auth()->user();

        $msg = sprintf('[DateLogger] => %s <%s> hat einen Termin für "%s" mit "%s" am %s vereinbart', $user->name, $user->email, $dentist->dentistmeta->name, $lab->name, $date->date);

        Log::info($msg);
//        Activity::log($msg);

        /* mailer('Zahnarzt-Labormail', $dentist, $lab, $date)
            ->toLab()
            ->fromSecondary()
            ->xtags('Labor, Termin')
            ->send(); */
    }

}
