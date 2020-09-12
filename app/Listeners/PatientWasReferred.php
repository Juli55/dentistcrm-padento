<?php

namespace App\Listeners;

use Log;
use Activity;
use App\Events\PatientReferred;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PatientWasReferred
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
     * @param  PatientReferred $event
     * @return void
     */
    public function handle(PatientReferred $event)
    {
        $patient = $event->patient;
        $lab = $patient->lab;

        $msg = sprintf('[PatientReferLogger] => "%s" <%s> [PLZ: %s] wurde an das Labor %s weitergeleitet.',
            $patient->patientmeta->name,
            $patient->patientmeta->email,
            $patient->patientmeta->zip,
            $lab->name
        );

        Log::info($msg);
//        Activity::log($msg);

        $mailName = ($lab->membership == 1 || $lab->membership == 4) ? 'Labormail2' : 'Labormail1';

        mailer($mailName, $patient, $lab)
            ->toLab()
            ->xtags('Labor, Patient weitergeleitet, per Hand')
            ->send();
    }
}
