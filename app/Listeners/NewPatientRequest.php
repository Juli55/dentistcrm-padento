<?php

namespace App\Listeners;

use Log;
use Activity;
use App\Patient;
use App\Events\PatientFormFilled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewPatientRequest
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
     * @param PatientFormFilled $event
     * @return void
     */
    public function handle(PatientFormFilled $event)
    {
        $patient = Patient::with('patientmeta')->find($event->patient_id);

        $msg = sprintf('[PatientLogger] => "%s" <%s> [PLZ: %s] [tel: %s] hat das Formular ausgefüllt',
            $patient->patientmeta->name,
            $patient->patientmeta->email,
            $patient->patientmeta->zip,
            $patient->patientmeta->tel
        );

        Log::info($msg);
//        Activity::log($msg);

        mailer('Kontaktmail1', $patient, null, null, $patient->token)
            ->toPatient()
            ->xtags('Patient, Bestätigungsmail, unbestätigt')
            ->shouldLogError()
            ->send();
    }
}
