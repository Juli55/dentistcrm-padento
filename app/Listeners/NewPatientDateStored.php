<?php

namespace App\Listeners;

use App;
use Auth;
use Log;
use Activity;
use App\Events\NewPatientDate;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewPatientDateStored
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
     * @param  NewPatientDate $event
     * @return void
     */
    public function handle(NewPatientDate $event)
    {
        $date    = $event->date;
        $patient = $date->patient;
        $lab     = $date->lab;
        $user    = auth()->user();

        $msg = sprintf('[DateLogger] => %s <%s> hat einen Termin für "%s" mit "%s" am %s vereinbart', $user->name, $user->email, $patient->patientmeta->name, $lab->name, $date->date);

        Log::info($msg);
//        Activity::log($msg);

        if ($lab->isQueueLab()) {
            mailer('Terminmail', $patient, $lab, $date)
                ->toPatient()
                ->fromSecondary()
                ->xtags('Patient, Termin')
                ->send();
        } else {
            //also normal contact dates without default labor
            mailer('Terminmail', $patient, $lab, $date)
                ->toPatient()
                ->fromSecondary()
                ->xtags('Patient, Termin')
                ->send();
        }



        if ($user->hasRole('admin') || $user->hasRole('user')) {
            /* mailer('Labormail2', $patient, $lab, $date)
                ->toLab()
                ->fromSecondary()
                ->xtags('Labor, Termin')
                ->send(); */
        }
    }

}
