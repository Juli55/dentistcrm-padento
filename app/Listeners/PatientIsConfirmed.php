<?php

namespace App\Listeners;

use App;
use Log;
use Activity;
use App\Events\PatientConfirmed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PatientIsConfirmed
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
     * @param  PatientConfirmed $event
     * @return void
     */
    public function handle(PatientConfirmed $event)
    {
        $patient = $event->patient;
        $lab = $patient->lab;

        $msg = sprintf('[PatientLogger] => "%s" <%s> [PLZ: %s] hat die seine Emailadresse bestätigt', $patient->patientmeta->name, $patient->patientmeta->email, $patient->patientmeta->zip);

       /* Log::info($msg); */
//        Activity::log($msg);

        if ($patient->queued == 1) {
            mailer('kontaktinqueue', $patient, $lab)->toPatient()->send();
        } else {
           mailer('Kontaktmail2', $patient, $lab)
                ->toPatient()
                ->xtags('Patient, Patienten Anfrage, bestätigt')
                ->send();
            mailer('Labormail1', $patient, $lab)
                ->toLab()
                ->xtags('Patient, Patienten Anfrage, bestätigt')
                ->send();
        }
    }
}
