<?php

namespace App\Http\Controllers;

use App\PatientMeta;
use Illuminate\Http\Request;

use App\Http\Requests;
use Mail;

class AccountController extends Controller
{
    public function index()
    {
        return view('account.remove');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:patient_metas',
        ]);

        $patient = PatientMeta::where('email', $request->email)->first()->patient;

        if ($patient) {
            mailer('DeleteAccount', $patient)->toPatient()->withoutDeleteLink()->send();

            session()->flash('msg', 'Wir haben Ihnen eine E-Mail zugesandt.');
        } else {
            session()->flash('msg', 'Etwas ist fehlgeschlagen.');
        }

        return back();
    }

    public function confirm(Request $request)
    {
        $patient = PatientMeta::where('email', $request->email)->where('patient_id', $request->id)->first()->patient;

        if ($patient) {
            $patient->requested_deletation();

            Mail::send('emails.default-mail', [
                'body'   => sprintf('%s möchte seine Daten gelöscht haben: <a href="%s">Kontakt ansehen</a>', $patient->patientmeta->name, url("app/kontakt/{$patient->id}")),
                'footer' => '',
            ], function ($message) {
                $message->to(config('mail.from.address'), config('mail.from.name'))->subject('Patient/Kontakt möchte gelöscht werden.');
            });
        }

        session()->flash('msg', 'Vielen Dank. Wir werden Ihre Daten in Kürze löschen.');

        return view('account.success');
    }
}
