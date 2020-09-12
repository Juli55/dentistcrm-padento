<?php

namespace App\Http\Controllers;

use App;
use Auth;
use Mail;
use Event;
use App\Patient;
use App\Attachment;
use Illuminate\Http\Request;
use App\Events\PatientConfirmed;
use App\DentistContact;

class PatientController extends Controller
{
    public function changePhase($patient, $phase)
    {
        if (!Auth::check()) {
            return abort(404);
        }
        $patient = Patient::find($patient);

        if ($phase == 6 && $patient->confirmed == 1) {
            $patient->unqueue();
            $patient->archive();

            $patient->removeDates();
            $patient->removeEmployeeDates();

            $lab = $patient->lab;

            mailer('patientHasNoInterestMail', $patient, $lab)
                ->toPatient()
                ->xtags('Patient, Patienten Anfrage, bestätigt')
                ->send();
        }

        if ($patient->confirmed == 0 && $phase == 6) {
            $patient->unqueue();
            $patient->archive();
            $patient->removeDates();
            $patient->removeEmployeeDates();
        }

        $patient->phase = $phase;
        $patient->save();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($patient)
            ->withProperties(['phase' => $patient->phase])
            ->log('phase_switched');

        return "patient phase set to {$phase}";
    }

    public function changePhaseForDentist($dentist, $phase)
    {
        if (!Auth::check()) {
            return abort(404);
        }
        $dentist = DentistContact::find($dentist);

        if ($dentist->queued == 1 && $phase == 6) {
            $dentist->queued = 0;

            /*$lab = $dentist->lab;*/

            activity()->causedBy(auth()->user())->performedOn($dentist)->log('unqueued');
            /*
            mailer('patientHasNoInterestMail', $dentist, $lab)
                ->toPatient()
                ->xtags('Patient, Patienten Anfrage, bestätigt')
                ->send();
            */
        }

        $dentist->phase = $phase;
        $dentist->save();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($dentist)
            ->withProperties(['phase' => $dentist->phase])
            ->log('phase_switched_dentist');

        return "patient phase set to {$phase}";
    }

    private function tidyDate($date)
    {
        if ($date) {
            return str_replace(' Uhr', '', $date);
        } else {
            return null;
        }
    }

    public function bestTime(Request $request)
    {
        $patient = Patient::with('patientmeta', 'lab.user')->where('id', $request['patient_id'])->first();

        $patient->workday_from = $this->tidyDate($request->from);
        $patient->workday_till = $this->tidyDate($request->till);
        $patient->weekend_from = $this->tidyDate($request->weekend_from);
        $patient->weekend_till = $this->tidyDate($request->weekend_till);

        $patient->save();

        if ($request->from || $request->till || $request->weekend_from || $request->weekend_till) {
            activity()->causedBy($patient)->performedOn($patient)->withProperties([
                'workday_from' => $patient->workday_from, 'workday_till' => $patient->workday_till,
                'weekend_from' => $patient->weekend_from, 'weekend_till' => $patient->weekend_till,
            ])->log('added_best_time');
        }

        for ($i = 1; $i <= 5; $i++) {
            if ($request->hasFile("attachment$i")) {
                $attachment = Attachment::upload($patient->id, $request->file("attachment$i"));

                activity()->causedBy($patient)->performedOn($patient)->withProperties(['attachment' => $attachment])->log('attachment_uploaded');
            }
        }

        return view('pages.mail-confirmed', ['request' => $request]);
    }

    public function mailtoken($token)
    {
        if ($token == 'Testtoken') {
            return "<h1 style=\"text-align: center;\">Testtoken wurde bestätigt</h1>";
        }
        $patient = Patient::with(['patientmeta', 'lab', 'lab.images'])
            /*  ->whereHas('lab',function ($q){
                    $q->whereHas('images',function ($query){
                         $query->where('type','kontaktfoto');
                    });
              })*/
            ->where('token', '=', $token)->first();

        if (!$patient) {
            return "Der Kontakt zu diesem Link ist nicht mehr vorhanden.";
        }

        if ($patient != '' && $patient->confirmed == 1) {
            $kontaktperson = $patient->lab->labmeta->contact_person;
            $id            = $patient->lab->id;
            $city          = $patient->lab->labmeta->city;
            $labor         = $patient->lab->slug;
            $labtel        = $patient->lab->labmeta->tel;
            $image         = $patient->lab->images->where('type', 'kontaktfoto')->first() ? 'storage/' . $patient->lab->images->where('type', 'kontaktfoto')->first()->path : 'images/logo.png';

            session(['patientConfirmed' => $patient]);

            return view('pages.mail-confirmed-is-in-queue', ['kontaktperson' => $kontaktperson, 'labtel' => $labtel, 'id' => $id, 'stadt' => $city, 'labor' => $labor, 'lab' => $patient->lab, 'image' => $image]);
        }
        if ($patient != '' && $patient->confirmed != '1') {
            $patient->confirmed = 1;
            $patient->save();

            activity()->causedBy($patient)->performedOn($patient)->log('patient_confirmed');

            if ($patient->membership != 1 && $patient->membership != 4) { //Wenn Patient NICHT einem Labor mit neuer Gruppe gehört
                $kontaktperson = $patient->lab->labmeta->contact_person;
                $id            = $patient->lab->id;
                $city          = $patient->lab->labmeta->city;
                $labor         = $patient->lab->slug;
                $labtel        = $patient->lab->labmeta->tel;
                $contactImage  = $patient->lab->images->where('type', 'kontaktfoto')->first();
                $image         = $contactImage ? 'storage/' . $contactImage->path : 'images/logo.png';

                session(['patientConfirmed' => $patient]);
                Event::fire(new PatientConfirmed($patient));

                return view('pages.mail-confirmed', ['kontaktperson' => $kontaktperson, 'labtel' => $labtel, 'id' => $id, 'stadt' => $city, 'labor' => $labor, 'lab' => $patient->lab, 'image' => $image]);
            } else {
                $kontaktperson = $patient->lab->labmeta->contact_person;
                $id            = $patient->lab->id;
                $city          = $patient->lab->labmeta->city;
                $labor         = $patient->lab->slug;
                $labtel        = $patient->lab->labmeta->tel;
                $contactImage2 = $patient->lab->images->where('type', 'kontaktfoto')->first();
                $image         = $contactImage2 ? 'storage/' . $contactImage2->path : 'images/logo.png';

                session(['patientConfirmed' => $patient]);

                Event::fire(new PatientConfirmed($patient));

                return view('pages.mail-confirmed-is-in-queue', ['kontaktperson' => $kontaktperson, 'labtel' => $labtel, 'id' => $id, 'stadt' => $city, 'labor' => $labor, 'lab' => $patient->lab, 'image' => $image]);
            }

            return $patient;
        }
    }
}
