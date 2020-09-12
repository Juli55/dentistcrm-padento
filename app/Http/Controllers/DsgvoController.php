<?php

namespace App\Http\Controllers;

use App\PatientMeta;
use Illuminate\Http\Request;

class DsgvoController extends Controller
{
    public function index(Request $request)
    {
        $patientMeta = PatientMeta::where('email', $request->email)->where('patient_id', $request->id)->with('patient')->first();


        if ($patientMeta) {
            $patient = $patientMeta->patient;
            if ($patient) {
                $patient->estimated_deletation_at = null;
                $patient->save();

                activity()->causedBy($patient)->performedOn($patient)->withProperties(['subject' => 'Daten weiterhin speichern', 'message' => 'Patient möchte seine Daten beibehalten'])->performedOn($patient)->log('accepted_dsgvo');

                if ($patient->lab) {
                    mailer('DSGVOAutoDeletePatientMailForLab', $patient, $patient->lab)->toLab()->send();
                }
            }
        }

        return redirect('/');
    }
}
