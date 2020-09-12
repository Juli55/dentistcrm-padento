<?php

namespace App\Http\Controllers;

use App;
use App\Http\Requests;
use App\Http\Requests\LabRequest;
use Auth;
use Illuminate\Http\Request;
use Mail;
use Uuid;

class LabController extends Controller
{
    public function show($name)
    {
        $lab = \App\Lab::where('slug', '=', $name)->firstOrFail();
        if (!$lab->user || $lab->user->allowlogin != 'allow') {
            return "Dieses Labor ist bei uns zur Zeit nicht aktiv.";
        }
        $id = $lab->id;

        $logoImage    = $lab->images()->where('type', 'logo')->first();
        $kontaktmage  = $lab->images()->where('type', 'kontaktfoto')->first();
        $bilderImages = $lab->images()->where('type', 'bilder')->orderBy('order')->get();
        $zertImages   = $lab->images()->where('type', 'zert')->orderBy('order')->get();

        $pic   = "/img/kontaktfotos/foto" . $id . "_neu.jpg";
        $logo  = "/img/logos/logo" . $id . "_neu.jpg";
        $count = 0;
        $lab->labmeta->count++;
        $lab->labmeta->save();

        if ($lab != '') {
            return view('pages.single-lab', ['lab' => $lab, 'pic' => $pic, 'logo' => $logo, 'count' => $count, 'logoImage' => $logoImage, 'kontaktImage' => $kontaktmage, 'bilderImages' => $bilderImages, 'zertImages' => $zertImages]);
        }

        return "Lab not found";
    }

    public function showDentist($name)
    {
        $dent = \App\Dentist::where('slug', '=', $name)->first();
        $logo = "/img/logo" . $dent->id . "_175small.jpg";

        if ($dent->status != 'aktiv') {
            return "Dieser Zahnarzt ist bei uns zur Zeit nicht aktiv.";
        };

        return view('pages.single-dent', ['lab' => $dent, 'logo' => $logo]);

        return $dent;
    }

    public function labRequest(Request $request)
    {
        if (App::environment('local')) {
            $email     = getDevMail();
            $name      = 'Dev Tester';
            $xtags     = 'Test, Labor, Registrierungsanfrage';
            $email_lab = getDevMail();
            $name_lab  = $request->input('kontaktperson');
            $xtags_lab = 'Test, Labor, Registrierungsanfrage Bestätigung';
        } else {
            $email     = 'info@padento.de';
            $name      = 'Rainer Ehrich';
            $xtags     = 'Labor, Registrierungsanfrage';
            $email_lab = $request->input('email');
            $name_lab  = $request->input('kontaktperson');
            $xtags_lab = 'Test, Labor, Registrierungsanfrage Bestätigung';
        }
        $from_name  = $request->input('kontaktperson');
        $from_email = $request->input('email');
        Mail::send('emails.labrequest', ['data' => $request->input()], function ($message) use ($email, $name, $xtags) {
            $message->to($email, $name)->subject('Neue Laboranfrage bei Padento');
            $message->getHeaders()->addTextHeader('X-MC-Tags', $xtags);
            $message->from('info@padento.de', 'Padento');
            // $message->from('info@padento.de', 'Padento');
        });
        if (App::environment('local')) {
            $email = getDevMail();
            $name  = $request->input('kontaktperson');
            $xtags = 'Test, Labor, Registrierungsanfrage Bestätigung';
        } else {
            $email = $request->input('email');
            $name  = $request->input('kontaktperson');
            $xtags = 'Test, Labor, Registrierungsanfrage Bestätigung';
        }

        $body = '<p>Sehr geehrte/r Frau/Herr ' . $name .
            '<br>vielen Dank für Ihr Interesse an Padento.' .
            '<br>Wir werden Sie in Kürze kontaktieren, um alle weiteren Details mit Ihnen zu besprechen.' .
            '<br>Mit freundlichen Grüßen' .
            '<br>Rainer Ehrich</p>';

        $footer = mailSignature() . dsgvoMessage();

        Mail::send('emails.default-mail', [
            'body'   => $body,
            'footer' => $footer,
        ], function ($message) use ($email, $name, $xtags) {
            $message->to($email, $name)->subject('Ihre Anfrage bei Padento');
            $message->from('info@padento.de', 'Rainer Ehrich');
            $message->getHeaders()->addTextHeader('X-MC-Tags', $xtags);
        });

        return view('pages.thanks-for-request', ['kontaktperson' => $request->input('kontaktperson')]);
    }

    private function lookup($string)
    {
        $string      = str_replace(" ", "+", urlencode($string));
        $string      = $string . '+Deutschland';
        $details_url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . $string . "&region=de&language=de";
        $ch          = curl_init();
        curl_setopt($ch, CURLOPT_URL, $details_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);
        if ($response['status'] != 'OK') {
            return null;
        }
        // try {
        //     $formatted_address = $response['results'][0]['formatted_address'];
        $geometry = $response['results'][0]['geometry'];
        //     $city = explode(' ', explode(',',$formatted_address)[0]);
        //     dd($city);
        // } catch (Exception $e) {
        //     $city = '';
        // }
        $longitude = $geometry['location']['lat'];
        $latitude  = $geometry['location']['lng'];
        $array     = [
            'latitude'  => $geometry['location']['lng'],
            'longitude' => $geometry['location']['lat'],
            // 'location_type'     => $geometry['location_type'],
            // 'formatted_address' => $formatted_address,
            // 'city'              => $city
        ];

        return $array;
    }

    // public function item();
    // {
    //     return view('pages.newlab');
    // }

//    public function createNewLab(LabRequest $request)
//    {
//
//        return $request->all();
//
//        $lab_request = $request->input();
//
//        $error     = [];
//        $set_error = false;
//        $user      = \App\User::where('email', $lab_request['email'])->count();
//
//        if ($user > 0) {
//            $error[]   = 'E-Mail-Adresse wird bereits verwendet.';
//            $set_error = true;
//        }
//        if ($lab_request['password'] != $lab_request['password_confirmation']) {
//            $error[]   = 'Ihre Passwörter stimmen nicht überein.';
//            $set_error = true;
//        }
//        if ($lab_request['email'] != $lab_request['email2']) {
//            $error[]   = 'Ihre E-Mail-Adressen stimmen nicht überein.';
//            $set_error = true;
//        }
//        if ($set_error == true) {
//            // session(['error' => $error]);
//            session()->flash('errors', $error);
//
//            // return Redirect::back();
//            return back();
//            // ->with(['old' => $request->input(), 'errors' => $error]);
//        }
//
//        $user             = new \App\User();
//        $user->email      = $lab_request['email'];
//        $user->name       = $lab_request['ansprechpartner'];
//        $user->allowlogin = 'allow';
//        $user->password   = bcrypt($lab_request['password']);
//        $user->save();
//
//        $user->roles()->attach('3');
//
//        $lab = new \App\Lab();
//
//        $lab->name   = $lab_request['labname'];
//        $lab->status = 'inaktiv';
//
//        $lab->user_id     = $user->id;
//        $lab->google_city = '';
//        $lab->directtoken = UUid::generate('4');
//        $lab->slug        = str_slug($lab_request['labname'], '-');
//        $geo              = getLocation($lab_request['plz']);
//        $lab->lat         = $geo['latitude'];
//        $lab->lon         = $geo['longitude'];
//
//        $lab->save();
//
//        $labmeta                 = new \App\LabMeta();
//        $labmeta->contact_person = $lab_request['ansprechpartner'];
//        $labmeta->contact_email  = $lab_request['email'];
//        $labmeta->street         = $lab_request['street'];
//        $labmeta->city           = $lab_request['city'];
//        $labmeta->zip            = $lab_request['plz'];
//
//        $lab->labmeta()->save($labmeta);
//        Auth::loginUsingId($user->id);
//
//        return redirect('app/labore/' . $lab->id);
//        // return view('pages.thanks-for-signup', ['kontaktperson' => $request->input('ansprechpartner')]);
//    }
}
