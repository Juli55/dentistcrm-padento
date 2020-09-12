<?php

namespace App\Listeners;

use App\Events\PatientPhaseChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PatientPhaseChange
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
     * @param  PatientPhaseChanged  $event
     * @return void
     */
    public function handle(PatientPhaseChanged $event)
    {
        //
    }
}
