<?php

namespace App\Listeners;

use Log;
use Activity;
use App\Events\PatientSelfContacted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PatientWasSelfContacted
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
     * @param PatientSelfContacted $event
     */
    public function handle(PatientSelfContacted $event)
    {
        $patient = $event->patient;
        $lab = $patient->lab;

        if ($patient->confirmed = 1) {

            $msg = sprintf('[PatientUnobtainableLogger] => "%s" <%s> [PLZ: %s] konnte von %s nicht erreicht werden und wurde an das Labor %s weitergeleitet. Patient hat eine Mail bekommen',
                $patient->patientmeta->name,
                $patient->patientmeta->email,
                $patient->patientmeta->zip,
                auth()->user()->name,
                $lab->name
            );

            Log::info($msg);
//            Activity::log($msg);

            mailer('Terminselbermachen', $patient, $lab)
                ->toPatient()
                ->xtags('Patient, Termin machen')
                ->send();
        } else {

            $msg = sprintf('[PatientUnobtainableLogger] => "%s" <%s> [PLZ: %s] konnte von %s nicht erreicht werden und wurde an das Labor %s weitergeleitet. Patient hat keine Mail bekommen',
                $patient->patientmeta->name,
                $patient->patientmeta->email,
                $patient->patientmeta->zip,
                auth()->user->name,
                $lab->name
            );

            Log::info($msg);
//            Activity::log($msg);
        }
    }
}
