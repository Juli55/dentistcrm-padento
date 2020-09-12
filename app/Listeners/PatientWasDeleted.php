<?php

namespace App\Listeners;

use App\Events\PatientDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Auth;
use Activity;

class PatientWasDeleted
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
     * @param  PatientReferred  $event
     * @return void
     */
    public function handle(PatientDeleted $event)
    {
        $patient = $event->patient;
        $msg = '[PatientDeletedLogger] => "' . $patient->patientmeta->name . '" <' . $patient->patientmeta->email . '> [PLZ:' . $patient->patientmeta->zip . '] [tel: ' . $patient->patientmeta->tel . '] wurde gelöscht.';
        Log::info($msg);
//        Activity::log($msg);
    }
}
