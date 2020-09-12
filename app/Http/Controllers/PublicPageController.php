<?php

namespace App\Http\Controllers;

use App\Console\Commands\SendDentistDateMails;
use App\Email;
use App\Events\ValidationFailed;
use App\Services\Setting;
use App\User;
use Illuminate\Http\Request;
use Uuid;
use Auth;
use App\Http\Requests;
use Log;
use Carbon\Carbon;
use Event;
use Activity;
use \App\Events\PlzIsMissing;
use \App\Events\NoLabWasFound;
use \App\Events\PatientFormFilled;
use \App\Events\PatientConfirmed;
use App\Padento\Helper;
use Excel;
use App\PatientMeta;
use App\Patient;
use App\Todo;
use App\LabMeta;

class PublicPageController extends Controller
{
    public function lookup($string, $lang = 'de')
    {
        return getLocation($string, $lang);
    }

    private function distance($lat1, $lon1, $lat2, $lon2, $unit = 'K')
    {
        if ($lat1 == $lat2 && $lon1 == $lon2) {
            return 0;
        }

        $dist = 0.0;
        $x1   = $lon1;
        $x2   = $lon2;
        $y1   = $lat1;
        $y2   = $lat2;
        // e = ARCCOS[ SIN(Breite1)*SIN(Breite2) + COS(Breite1)*COS(Breite2)*COS(Länge2-Länge1) ]
        $dist = acos(sin($x1 = deg2rad($x1)) * sin($x2 = deg2rad($x2)) + cos($x1) * cos($x2) * cos(deg2rad($y2) - deg2rad($y1))) * (6378.137);

        return $dist;

        $theta = $lon1 - $lon2;
        $dist  = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist  = acos($dist);
        $dist  = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit  = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } elseif ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function sortByDist($a, $b)
    {
        return $a['dist'] - $b['dist'];
    }

    public function sortByPatientCount($a, $b)
    {
        return $a['patient_count_last_seven_days'] - $b['patient_count_last_seven_days'];
    }

    public function direct(Request $request, $id)
    {
        $request->session()->forget('direct');

        $lab = \App\Lab::with('labmeta')->where('directtoken', $id)->orWhere('slug', $id)
            ->orWhereHas('slugs', function ($q) use ($id) {
                $q->where('slug', $id);
            })->first();

        if ($lab != '') {
            session(['direct' => $lab->id]);
        }
        if (!is_object($lab)) return redirect('/'); 
        if (!is_object($lab->labmeta)) return redirect('/');
        if (!$lab->labmeta || $lab->labmeta->country_code == 'de') {
            return redirect('/');
        } else {
            return redirect($lab->labmeta->country_code);
        }
    }

    public function pickLab($lookup, $mail = null, $lang = 'de')
    {
        $oldLab = '';
        $old    = '';
        if ($mail != null) {
            // $old = \App\PatientMeta::where('email', '=', $mail)->first();
            $patient = \App\Patient::with('lab')->whereHas('patientmeta', function ($query) use ($mail) {
                $query->where('email', '=', $mail);
            })->orderBy('created_at', 'desc')->first();
            if ($patient != '' && $patient->lab != '' && $patient->lab->status == 'aktiv') {
                $dist     = $this::distance($lookup['latitude'], $lookup['longitude'], $patient->lab->lat, $patient->lab->lon);
                $distance = \App\Settings::where('name', '=', 'Entfernung für ab wann ein Kontakt ein neues Labor bekommen soll')->first()->value;
                if ($dist < $distance) {
                    $picked = ['lab' => ['lab' => $patient->lab, 'dist' => $dist]];

                    return $picked;
                }
            }
            /*
            if ($lang == 'at') {
                $labs = LabMeta::where('country_code', 'at')->get();
                $random = rand(0, $labs->count()-1);
                $pickedlab = $labs[$random]->lab;
                $picked = ['lab' => ['lab' => $pickedlab, 'dist' => '0']];
                return $picked;
            } */
        }

        //$labs = \App\Lab::with('patients')->where('status', '=', 'aktiv')->get();
        $radius_start = \App\Settings::where('name', '=', 'Patientenradius Start')->first()->value;
        $radius_inc   = \App\Settings::where('name', '=', 'Patientenradius Inkrementierung')->first()->value;
        $radius_max   = \App\Settings::where('name', '=', 'Patientenradius Ende')->first()->value;
        $labs = \App\Lab::whereHas('labmeta', function ($query) use ($lang) {
            $query->where('country_code', '=', $lang);
        })->with(['labmeta'])->where('status', '=', 'aktiv')->get();

        // dd( $this::distance(8.9630488, 51.8604282, 8.7504202, 51.897825) );
        // dd($lookup);
        for ($i = $radius_start; $i <= $radius_max; $i += $radius_inc) {
            $picked = [];
            foreach ($labs as $lab) {
                $dist = $this::distance($lookup['latitude'], $lookup['longitude'], $lab->lat, $lab->lon);
                if ($dist <= $i && !is_nan($dist)) {
                    $picked[] = ['lab' => $lab, 'dist' => $dist, 'i' => $i];
                }
            }
            if (sizeof($picked) > 0) {
                break;
            }
        }
        // dd($lab);
        // dd($picked);

        usort($picked, [$this, 'sortByDist']);
        return $picked;
    }

    private function checkPatients($labs)
    {
        $checked = [];
        $days    = \App\Settings::where('name', '=', 'Zeitraum zur Überprüfung der Patientenanzahl')->first()->value;
        foreach ($labs as $lab) {
            // $count = \App\Lab::find($lab['lab']['id'])->patients()->where('created_at', '>', Carbon::now()->subDays($days))->count();
            // $count = \App\Patient::where('lab_id', $lab['lab']['id'])->where('created_at', '>', Carbon::now()->subDays($days))->count();

            $count     = \App\Patient::where('lab_id', $lab['lab']->id)
                ->where('created_at', '>', Carbon::now()->subDays($days))
                ->count();
            $checked[] = ['lab' => $lab['lab'], 'dist' => $lab['dist'], 'patient_count_last_seven_days' => $count];
            usort($checked, [$this, 'sortByPatientCount']);
        }

        return $checked;
    }

    public function circleSettings()
    {
        $radius_start = \App\Settings::where('name', '=', 'Patientenradius Start')->first()->value;
        $radius_inc   = \App\Settings::where('name', '=', 'Patientenradius Inkrementierung')->first()->value;
        $radius_max   = \App\Settings::where('name', '=', 'Patientenradius Ende')->first()->value;

        return [
            'radius_start' => $radius_start,
            'radius_inc'   => $radius_inc,
            'radius_max'   => $radius_max,
        ];
    }

    public function addWelcomeVideoSetting ()
    {
      if(! \App\Settings::where('name', 'introduction vimeo welcome video')->exists()) {
          \App\Settings::firstOrCreate([
              'name' => 'introduction vimeo welcome video',
              'value' => "https://player.vimeo.com/video/302066309?color=00aff5&title=0&byline=0&portrait=0",
          ]);
      }
    }

    public function addFormVideoSetting ()
    {
      if(! \App\Settings::where('name', 'Padento Formular Video')->exists()) {
          \App\Settings::firstOrCreate([
              'name' => 'Padento Formular Video',
              'value' => "//fast.wistia.net/embed/iframe/k41j7v41lk?videoFoam=true",
          ]);
      }
    }

    public function startpage($lang = 'de')
    {
        $setting = new Setting($lang);

        $data = [
            'lang'               => $lang,
            'welcomeVideo'       => $setting->getWelcomeVideo(),
            'formVideo'          => $setting->getFormVideo(),
            'heading'            => $setting->getStartPageHeading(),
            'welcomeParagraph'   => $setting->getStartPageParagraph(),
            'findBestDentures'   => $setting->getFindBestDentures(),
            'startPageVideoCode' => $setting->getStartPageVideoCode(),
            'sequence'           => $setting->getSequence(),
            'formData'           => $setting->getFormData(),
        ];

        // return view('/pages/startpage', $data);
        return redirect("/lp/");
    }

    public function formpage()
    {
        $lang = 'de';
        $setting = new Setting($lang);
        return view('pages.formpage')->with(['formData' => $setting->getFormData(), 'lang' => $lang]);
    }

    public function newRequest(Request $request)
    {
        $lang = 'de';
        $setting = new Setting($lang);
        /*if(!in_array($request->server('HTTP_HOST'), ['padento.de', 'padento.devv', 'padento.test'])) {
            return 'You are not allowed to make this request.';
        }*/

        $inList = Helper::zipIsInList($request->plz, $lang);
        if (!$inList) {
            $inList = Helper::zipIsInList($request->plz, 'at');
            if($inList) {
                $lang = 'at';
            }
        }

        $country_code = strtoupper($lang) ?: 'DE';

        $lookup = [];

        if ($request->ajax()) {
            $p   = $request->input('contact');
            $lab = \App\Lab::find($p['labid']);

            $patient            = new \App\Patient();
            $patient->token     = Uuid::generate(4);
            $patient->phase     = $lab->isQueueLab() ? 2 : 1;
            $patient->confirmed = 1;

            $patient->save();

            if ($request->dsgvo) {
                $patient->acceptDsgvo();
            }

            $lookup = getLocation($request->plz, $lang); //Get Lat Long of PLZ
            $salutation       = isset($p['salutation']) ? $p['salutation'] : '';
            $meta             = new \App\PatientMeta();
            $meta->zip        = $p['zip'];
            $meta->city       = ''; // array_get($lookup, 'city');
            $meta->name       = $p['name'];
            $comparsed_email = str_replace('..', '.', $p['email']);
            $comparsed_email = str_replace(' ', '', $comparsed_email);
            $meta->email      = trim($comparsed_email);
            $meta->salutation = $salutation;
            $meta->tel        = '';
            if ($p['phone'] != '') {
                $meta->tel        = phone_format($p['phone'], $country_code);
            }
            $meta->ref        = 'Direkteingabe';
            $meta->orig_ref   = 'Direkteingabe';
            $meta->orig_page  = 'Direkteingabe';
            $meta->mobile     = '';

            $patient->patientmeta()->save($meta);

            $patient->moveToLab($lab);

            $patient->fresh()->createToDos();

            activity()->on($patient)->withProperties(['creator' => $request->user()->name])->log('added_contact_manually');
//            Event::fire(new PatientConfirmed($patient));

            if (!$lab->isQueueLab()) {
                mailer('Terminselbermachen', $patient, $lab)->toPatient()->xtags('Patient, Termin machen')->send(); // TODO: sollte nicht ausgeführt weden
                //mailer('Labormail2', $patient, $lab)->toLab()->fromSecondary()->xtags('Labor, Termin')->send();
            }

            return ['patient_id' => $patient->id];
        }
        $validator = \Validator::make($request->all(), [
            'plz'  => 'required|max:6|min:4',
            'name' => 'required|max:64',
            'mail' => 'required|max:128',
            'tel'  => 'required|phone:DE|max:128'
        ]);

        if ($validator->fails()) {
            event(new ValidationFailed($request->only('plz', 'name', 'mail', 'tel')));

            return view('pages.formpage')->with(['formData' => $setting->getFormData(), 'lang' => $lang])->withErrors($validator)->withInput($request->all());
        }

        if ($request->session()->has('direct')) {
            $lab['lab'] = \App\Lab::find($request->session()->get('direct'));
        } else {
            $inList = Helper::zipIsInList($request->plz, $lang);
            if (!$inList) {
                $inList = Helper::zipIsInList($request->plz, 'at');
                if($inList) {
                    $lang = 'at';
                }
            }
            if (!$inList) {
                Event::fire(new PlzIsMissing($request->plz));
                // return "Es wurde leider kein Labor in Ihrer näheren Umgebung gefunden.";
                // return redirect()->back()->withInput()->withErrors(['msg' => 'Diese PLZ gibt es scheinbar nicht.']);
            } else {
                $count = 3;
                while ($count) {
                    $lookup     = getLocation($request->plz, $lang); //Get Lat Long of PLZ
                    $pickedLabs = $this::pickLab($lookup, $request->mail, $lang); //Get Labs

                    if (count($pickedLabs) > 0 || !$count) {
                        break;
                    }

                    $count--;
                }

                $checkedLabs = $this::checkPatients($pickedLabs); //Get Labs sorted by contacts in the last x

            }

            if (!isset($checkedLabs) || sizeof($checkedLabs) < 1) {
                $patient             = new \App\Patient();
                $patient->token      = Uuid::generate(4);
                $patient->phase      = 1;
                $patient->membership = '0';
                $patient->save();

                if ($request->dsgvo) {
                    $patient->acceptDsgvo();
                }

                $meta             = new \App\PatientMeta();
                $meta->zip        = $request->plz;
                $meta->city       = ''; // array_get($lookup, 'city');
                $meta->salutation = $request->salutation;
                $meta->name       = $request->name;
                $comparsed_email = str_replace('..', '.', $request->mail);
                $comparsed_email = str_replace(' ', '', $comparsed_email);
                $meta->email      = trim($comparsed_email);
                $meta->tel        = phone_format($request->tel, $country_code);
                if($request->tel != '')  {
                    $meta->tel        = phone_format($request->tel, $country_code);
                }
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $meta->ref = htmlentities($_SERVER['HTTP_REFERER']);
                } else {
                    $meta->ref = 'Unbekannt';
                }
                if (isset($_COOKIE['orig_ref'])) {
                    $meta->orig_ref = htmlentities($_COOKIE['orig_ref']);
                } else {
                    $meta->orig_ref = 'Unbekannt';
                }
                if (isset($_COOKIE['orig_page'])) {
                    $meta->orig_page = htmlentities($_COOKIE['orig_page']);
                } else {
                    $meta->orig_page = 'Unbekannt';
                }

                $meta->mobile = '';
                $patient->patientmeta()->save($meta);

                $patient->fresh()->createToDos();

                Event::fire(new NoLabWasFound($request->name, $request->tel, $request->plz));

                return view('pages.no-lab-found');
            }
            $lab = $checkedLabs[0];
        }

        $patient        = new \App\Patient();
        $patient->token = Uuid::generate(4);
        if ($request->session()->has('direct')) {
            $patient->direct = 1;
        }
        $patient->phase = 1;

        $patient->unqueue();

        if ($lab['lab']['membership'] == 1 || $lab['lab']['membership'] == 4) {
            $patient->queued = 1;
        }

        $patient->lab_id     = $lab['lab']['id'];
        $patient->membership = $lab['lab']['membership'];

        $patient->save();

        if ($request->dsgvo) {
            $patient->acceptDsgvo();
        }

        $meta             = new \App\PatientMeta();
        $meta->zip        = $request->plz;
        $meta->city       = ''; // array_get($lookup, 'city');
        $meta->salutation = $request->salutation;
        $meta->name       = $request->name;
        $comparsed_email = str_replace('..', '.', $request->mail);
        $comparsed_email = str_replace(' ', '', $comparsed_email);
        $meta->email      = trim($comparsed_email);
        $meta->tel        = phone_format($request->tel, $country_code);
        $meta->tel        = $request->tel;
        if ($request->tel != '') {
            $meta->tel        = phone_format($request->tel, $country_code);
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            $meta->ref = htmlentities($_SERVER['HTTP_REFERER']);
        } else {
            $meta->ref = 'Unbekannt';
        }
        if (isset($_COOKIE['orig_ref'])) {
            $meta->orig_ref = htmlentities($_COOKIE['orig_ref']);
        } else {
            $meta->orig_ref = 'Unbekannt';
        }
        if (isset($_COOKIE['orig_page'])) {
            $meta->orig_page = htmlentities($_COOKIE['orig_page']);
        } else {
            $meta->orig_page = 'Unbekannt';
        }

        $meta->mobile = '';
        $patient->patientmeta()->save($meta);

        $patient->fresh()->createToDos();

        Event::fire(new PatientFormFilled($request->name, $request->mail, $request->plz, $patient->id));

        return view('pages.info-post-request');
    }

    public function newDentistContact(Request $request)
    {
        $lang = $request->input('lang');

        $country_code = strtoupper($lang) ?: 'DE';

        $lookup = [];

        if ($request->ajax()) {
            $p = $request->input('contact');
            $salutation = isset($p['salutation']) ? $p['salutation'] : "";

            $dentist            = new \App\DentistContact();
            $dentist->token     = Uuid::generate(4);
            $dentist->phase     = 1;
            $dentist->confirmed = 1;
            $dentist->queued    = 0;
            $dentist->lab_id    = $p['labid'];

            $dentist->save();

            $meta      = new \App\DentistContactMeta();
            $meta->zip = $p['zip'];
//            $meta->city       = $p['city'];
            $meta->name       = $p['name'];
            $comparsed_email = str_replace('..', '.', $p['email']);
            $comparsed_email = str_replace(' ', '', $comparsed_email);
            $meta->email      = trim($comparsed_email);
            $meta->salutation = $salutation;
            $meta->tel        = phone_format($p['phone'], $country_code);
            $meta->ref        = 'Direkteingabe';
            $meta->orig_ref   = 'Direkteingabe';
            $meta->orig_page  = 'Direkteingabe';
            $meta->mobile     = '';

            $dentist->dentistmeta()->save($meta);

            $todos = Todo::whereNull('contact_id')->where('is_queued', $dentist->queued)->get();
            foreach ($todos as $todoItem) {
                $todo             = new Todo();
                $todo->contact_id = $dentist->id;
                $todo->title      = $todoItem->title;
                $todo->is_queued  = $todoItem->is_queued;
                $todo->order      = $todoItem->order;
                $todo->creator_id = $todoItem->creator_id;
                $todo->save();
            }

            return ['dentist_id' => $dentist->id];
        }

        $validator = \Validator::make($request->all(), [
            'plz'  => 'required|max:6|min:4',
            'name' => 'required|max:64',
            'mail' => 'required|max:128',
            'tel'  => 'required|phone:DE|max:128',
        ]);

        if ($validator->fails()) {
            event(new ValidationFailed($request->only('plz', 'name', 'mail', 'tel')));

            return back()->withErrors($validator, $request->form_name)->withInput($request->all());
        }

        if ($request->session()->has('direct')) {
            $lab['lab'] = \App\Lab::find($request->session()->get('direct'));
        } else {
            $inList = Helper::zipIsInList($request->plz, $lang);
            if ($inList == false) {
                Event::fire(new PlzIsMissing($request->plz));
                // return "Es wurde leider kein Labor in Ihrer näheren Umgebung gefunden.";
                // return redirect()->back()->withInput()->withErrors(['msg' => 'Diese PLZ gibt es scheinbar nicht.']);
            } else {
                $lookup      = getLocation($request->plz, $lang); //Get Lat Long of PLZ
                $pickedLabs  = $this::pickLab($lookup, $request->mail, $lang); //Get Labs
                $checkedLabs = $this::checkPatients($pickedLabs); //Get Labs sorted by contacts in the last x
            }

            if (!isset($checkedLabs) || sizeof($checkedLabs) < 1) {
                $dentist             = new \App\DentistContact();
                $dentist->token      = Uuid::generate(4);
                $dentist->phase      = 1;
                $dentist->membership = '0';
                $dentist->save();

                $meta             = new \App\DentistContactMeta();
                $meta->zip        = $request->plz;
                $meta->city       = ''; // array_get($lookup, 'city');
                $meta->salutation = $request->salutation;
                $meta->name       = $request->name;
                $comparsed_email = str_replace('..', '.', $request->mail);
                $comparsed_email = str_replace(' ', '', $comparsed_email);
                $meta->email      = trim($comparsed_email);
                $meta->tel        = phone_format($request->tel, $country_code);
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $meta->ref = htmlentities($_SERVER['HTTP_REFERER']);
                } else {
                    $meta->ref = 'Unbekannt';
                }
                if (isset($_COOKIE['orig_ref'])) {
                    $meta->orig_ref = htmlentities($_COOKIE['orig_ref']);
                } else {
                    $meta->orig_ref = 'Unbekannt';
                }
                if (isset($_COOKIE['orig_page'])) {
                    $meta->orig_page = htmlentities($_COOKIE['orig_page']);
                } else {
                    $meta->orig_page = 'Unbekannt';
                }

                $meta->mobile = '';
                $dentist->dentistmeta()->save($meta);

                $todos = Todo::whereNull('contact_id')->where('is_queued', 0)->get();
                foreach ($todos as $todoItem) {
                    $todo             = new Todo();
                    $todo->contact_id = $dentist->id;
                    $todo->title      = $todoItem->title;
                    $todo->is_queued  = $todoItem->is_queued;
                    $todo->order      = $todoItem->order;
                    $todo->creator_id = $todoItem->creator_id;
                    $todo->save();
                }

                Event::fire(new NoLabWasFound($request->name, $request->tel, $request->plz));

                return view('pages.no-lab-found');
            }
            $lab = $checkedLabs[0];
        }

        $dentist        = new \App\DentistContact();
        $dentist->token = Uuid::generate(4);
        if ($request->session()->has('direct')) {
            $dentist->direct = 1;
        }
        $dentist->phase = 1;

        $dentist->queued = 0;

        if ($lab['lab']['membership'] == 1 || $lab['lab']['membership'] == 4) {
            $dentist->queued = 1;
        }

        $dentist->lab_id     = $lab['lab']['id'];
        $dentist->membership = $lab['lab']['membership'];

        $dentist->save();

        $meta             = new \App\DentistContactMeta();
        $meta->zip        = $request->plz;
        $meta->city       = ''; // array_get($lookup, 'city');
        $meta->salutation = $request->salutation;
        $meta->name       = $request->name;
        $comparsed_email = str_replace('..', '.', $request->mail);
        $comparsed_email = str_replace(' ', '', $comparsed_email);
        $meta->email      = trim($comparsed_email);
        $meta->tel        = phone_format($request->tel, $country_code);
        if (isset($_SERVER['HTTP_REFERER'])) {
            $meta->ref = htmlentities($_SERVER['HTTP_REFERER']);
        } else {
            $meta->ref = 'Unbekannt';
        }
        if (isset($_COOKIE['orig_ref'])) {
            $meta->orig_ref = htmlentities($_COOKIE['orig_ref']);
        } else {
            $meta->orig_ref = 'Unbekannt';
        }
        if (isset($_COOKIE['orig_page'])) {
            $meta->orig_page = htmlentities($_COOKIE['orig_page']);
        } else {
            $meta->orig_page = 'Unbekannt';
        }

        $meta->mobile = '';
        $dentist->dentistmeta()->save($meta);

        $todos = Todo::whereNull('contact_id')->where('is_queued', $dentist->queued)->get();
        foreach ($todos as $todoItem) {
            $todo             = new Todo();
            $todo->contact_id = $dentist->id;
            $todo->title      = $todoItem->title;
            $todo->is_queued  = $todoItem->is_queued;
            $todo->order      = $todoItem->order;
            $todo->creator_id = $todoItem->creator_id;
            $todo->save();
        }

        Event::fire(new PatientFormFilled($request->name, $request->mail, $request->plz, $dentist->id));

        return view('pages.info-post-request');
    }

    public function videos($lang = 'de')
    {
      $setting = new Setting($lang);

      $data = [
          'lang'               => $lang,
          'formData'           => $setting->getFormData(),
      ];
      return view('pages.videos', $data);
    }

    public function impressum()
    {
        return view('pages.impressum');
    }

    public function datenschutzerklaerung()
    {
        return view('pages.datenschutzerklaerung');
    }

    public function participate()
    {
        return view('pages.request-membership');
    }

    public function participateSend()
    {
        # code...
    }

    public function testplz($plz, $lang = 'de', $mail = null)
    {
        $patient = '';
        $inList  = Helper::zipIsInList($plz, $lang);
        if ($inList == false) {
            return 'Postleitzahl nicht in der Liste';
        }
        $lookup      = getLocation($plz, $lang); //Get Lat Long of PLZ
        $pickedLabs  = $this::pickLab($lookup, $mail, $lang); //Get Labs
        $checkedLabs = $this::checkPatients($pickedLabs); //Get Labs sorted by contacts in the last x days
        if ($mail != null) {
            $patient = \App\Patient::with(['lab', 'patientmeta'])->whereHas('patientmeta', function ($query) use ($mail) {
                $query->where('email', '=', $mail);
            })->orderBy('created_at', 'desc')->first();
        }

        return [
            'patient' => $patient,
            'labs'    => $checkedLabs,
        ];
    }

//    public function refreshLatLon()
//    {
//        $labs = \App\Lab::with('labmeta')->get();
//        foreach ($labs as $lab) {
//            if (isset($lab->labmeta) && isset($lab->labmeta->zip)) {
//                $zip    = $lab->labmeta->zip;
//                $coords = getLocation($zip);
//                echo "{$lab->lat} => {$coords['latitude']} / {$lab->lon} => {$coords['longitude']} <br>";
//                $lab->lat = $coords['latitude'];
//                $lab->lon = $coords['longitude'];
//                $lab->save();
//            }
//        }
//    }

    public function cron()
    {
        if ($_SERVER['REMOTE_ADDR'] != '85.13.157.244') {
            $msg = $_SERVER['REMOTE_ADDR'] . ' versucht gerade die Seite vom Cronjob aufzurufen.';
            \Mail::send('emails.system-mail', ['msg' => $msg], function ($message) {
                $message->from('info@padento.de', 'Padento');
                $message->getHeaders()->addTextHeader('X-MC-Tags', 'Fehler, Systemmail, Mails');
                $message->to(getDevMail(), 'Marc')->subject('[Padento] Jemand versucht den Cronjob aufzurufen!');
            });

            return abort(404);
        }
        \Artisan::call('cronmail:lastday');
        \Artisan::call('crondates:nextday');
        echo "ok";
    }

    public function testform()
    {
        return view('testform');
    }

    public function test()
    {
//        return (new SendDentistDateMails())->handle();
//        $activity = \App\Activity::first();
//        return view('activities.contact_created', compact('activity'));
        dd(base_path(), storage_path());

//        activity()->causedBy($user)->performedOn($patient)->log('contact_created');
//        activity()->causedBy($user)->performedOn($patient)->withProperties(['phase' => $patient->phase])->log('phase_switched');
//        activity()->causedBy($user)->performedOn($patient)->withProperties(['message' => 'Test message'])->log('sms_sent');
//        activity()->causedBy($user)->performedOn($patient)->withProperties(['subject' => Email::first()->subject])->log('mail_sent');
    }
}
