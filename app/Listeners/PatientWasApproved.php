<?php

namespace App\Listeners;

use App;
use App\Events\PatientHasBeenApproved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log, Activity;
use Mail;
use Auth;
use App\Padento\Helper;

class PatientWasApproved
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
     * @param  PatientHasBeenApproved  $event
     * @return void
     */
    public function handle(PatientHasBeenApproved $event)
    {
        $patient = $event->patient;
        $msg = '[PatientLogger] => "' . $patient->name . '" <' . $patient['patientmeta']['email'] . '> [PLZ: ' . $patient['patientmeta']['zip'] . '] wurde als bestätigt markiert';
        Log::info($msg);
//        Activity::log($msg);

        $email = \App\Email::where('name', '=', 'Kontaktmail2')->first();
        $patient = \App\Patient::find($patient->id);

        if ($patient->phase <= 2) {
            $patient->phase = 2;
            $patient->save();
        }

        /* Important Notice:
         * rather comment that stuff out than deactivate the firing of an event (to keep logs consistent)
         *

        $patient_address = $patient->patientmeta->email;
        $lab_address = "pascal@pinetco.com";
        $patient_name = $patient->patientmeta->name;
        $lab = $patient->lab;
        $kontaktperson = $lab->labmeta->contact_person;
        $body = $email->body;
        $body = Helper::markdown($body, $lab, $patient);
        $subject = Helper::markdown($email->subject, $lab, $patient);
        $footer = $email->footer;
        $footer = Helper::markdown($footer, $lab, $patient);

        if (App::environment('local')) {
            $lab_address = env('DEV_MAIL', 'pascal@pinetco.com');
            $patient_address = env('DEV_MAIL', 'pascal@pinetco.com');
            $xtags_patient = 'Test, Patient, Patienten Anfrage, bestätigt, per Hand';
            $xtags_lab = 'Test, Labor, Patienten Anfrage, bestätigt, per Hand';
        } else {
            $lab_address = $lab->user->email;
            $patient_address = $patient->patientmeta->email;
            $xtags_patient = 'Patient, Patienten Anfrage, bestätigt, per Hand';
            $xtags_lab = 'Labor, Patienten Anfrage, bestätigt, per Hand';
        }

        try {
            Mail::send('emails.kontakt2', [
                'body' => $body,
                'footer' => $footer
            ], function($message) use ($patient_address, $patient_name, $subject, $xtags_patient) {
                $message->to($patient_address, $patient_name)->subject($subject);
                $message->getHeaders()->addTextHeader('X-MC-Tags', $xtags_patient);
                $message->from('info@padento.de', 'Padento');
            });
        } catch(Exception $e) {
            $msg = "Leider gab es einen Fehler mit " . $patient_address;
            Mail::send( 'emails.system-mail', ['msg' => $msg], function($message) {
                $message->from('info@padento.de', 'Padento');
                $message->getHeaders()->addTextHeader('X-MC-Tags', 'Fehler, Systemmail, Mails');
                $message->to(env('DEV_MAIL', 'pascal@pinetco.com'), 'Marc')->subject('[Padento] Mails gehen nicht raus!');
            });
        }
        $email = \App\Email::where('name', '=', 'Labormail1')->first();
        $body = $email->body;
        $body = Helper::markdown($body, $lab, $patient);
        $subject = strip_tags(Helper::markdown($email->subject));
        $footer = $email->footer;
        $footer = Helper::markdown($footer, $lab, $patient);
        try {
            Mail::send('emails.default-mail', [
                'name' => $lab->labmeta->contact_person,
                'body' => $body,
                'footer' => $email->footer
            ], function($message) use ($lab_address, $patient_name, $subject, $xtags_lab) {
                $message->to($lab_address, $patient_name)->subject($subject);
                $message->getHeaders()->addTextHeader('X-MC-Tags', $xtags_lab);
                $message->from('info@padento.de', 'Padento');
            });
        } catch(Exception $e) {
            $msg = "Leider gab es einen Fehler mit " . $lab_address;
            Mail::send( 'emails.system-mail', ['msg' => $msg], function($message) {
                $message->from('info@padento.de', 'Padento');
                $message->getHeaders()->addTextHeader('X-MC-Tags', 'Fehler, Systemmail, Mails');
                $message->to(env('DEV_MAIL', 'pascal@pinetco.com'), 'Marc')->subject('[Padento] Mails gehen nicht raus!');
            });
        }
        */

    }
}
