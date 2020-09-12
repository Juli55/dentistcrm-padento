<?php

namespace App\Listeners;

use App\Events\PropertiesChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncProperties
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
     * @param  PropertiesChanged  $event
     * @return void
     */
    public function handle(PropertiesChanged $event)
    {
        #$activeProps = \App\PatientProp::where('status', '=', 'Aktiv')->get();
        $activeProps = \App\PatientProp::all();
        $patients = \App\Patient::all();

        foreach ($patients as $pat) {
            foreach ($activeProps as $prop) {
                $check = \App\PropPivot::firstOrCreate(['patient_id' => $pat->id, 'patient_prop_id' => $prop->id]);
                $check->status = $prop->status;
                if ($check->value == '') {
                    $check->value = $prop->default;
                }
                $check->save();
            }
        }
    }
}
