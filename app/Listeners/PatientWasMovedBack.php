<?php

namespace App\Listeners;

use Log;
use Activity;
use App\Events\PatientMovedBack;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PatientWasMovedBack
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
     * @param PatientMovedBack $event
     */
    public function handle(PatientMovedBack $event)
    {
        $event = reset($event);
        $patient = $event[0];
        $type = $event[1];
        $lab = $patient->lab;

        if ($type == 'canceled') {
            $mailName = 'CanceledDate';
            $msg = sprintf('[PatientMovedBackLogger] => "%s" [%s] hat den Termin abgesagt und wurde an das Padento-Team zurückgeleitet', $patient->patientmeta->name, $patient->id);
        } else {
            $mailName = 'MissedDate';
            $msg = sprintf('[PatientMovedBackLogger] => "%s" [%s] ist nicht beim Termin erschienen und wurde an das Padento-Team zurückgeleitet', $patient->patientmeta->name, $patient->id);
        }

        Log::info($msg);
//        Activity::log($msg);

        mailer($mailName, $patient, $lab)
            ->toPatient()
            ->fromSecondary()
            ->xtags('Patient, Nicht beim Termin erschienen')
            ->send();
    }
}
