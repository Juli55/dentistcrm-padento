<?php

namespace App\Http\Controllers\API;

use App\Events\PatientReferred;
use App\Events\PatientSelfContacted;
use App\Events\PatientUnobtainable;
use App\PatientNote;
use App\PatientsUsed;
use App\Settings;
use DB;
use Auth;
use Mail;
use Event;
use Helper;
use App\Lab;
use App\Date;
use App\Email;
use App\Patient;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Events\NewPatientDate;
use App\Events\PatientDeleted;
use App\Events\PatientMovedBack;
use App\Http\Controllers\Controller;
use App\Events\PatientHasBeenApproved;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function allcontacts(Request $request)
    {
        $user = $request->user();

        if ($request->input('pagination')['per_page']) {
            $per_page = $request->input('pagination')['per_page'];
        } else {
            $per_page = 100;
        }

        $searchThrough = [
            'patientmeta.name',
//            'patientmeta.email',
            'patientmeta.tel',
            'patientmeta.mobile',
            'patientmeta.zip',
            'patientmeta.city',
            // 'created_at',
            'id',
        ];

        //SELECT patients.*, MIN(dates.date) FROM patients LEFT JOIN dates ON patients.id = dates.patient_id AND dates.date > NOW() GROUP BY patients.id;
        $results = Patient::select([
            'patients.*',
            DB::raw('MAX(dates.date) as labDate'),
            DB::raw('MIN(employee_dates.date) as empDate'),
        ]);
        $results = $results->join('patient_metas as patientmeta', 'patientmeta.patient_id', '=', 'patients.id');

        $results = $results->leftJoin('dates', function ($query) {
            $query->on('patients.id', '=', 'dates.patient_id');
            $query->whereNull('dates.deleted_at');
            //$query->on('dates.date', '>', DB::raw('NOW()'));
        });
        $results = $results->leftJoin('employee_dates', function ($query) {
            $query->on('patients.id', '=', 'employee_dates.patient_id');
            $query->whereNull('employee_dates.deleted_at');
            // $query->on('employee_dates.date', '>', DB::raw('NOW()'));
        });
        $results = $results->leftJoin('labs', function ($query) {
            $query->on('patients.lab_id', '=', 'labs.id');
        });

        if ($request->input('orderby')) {
            $results = $results->orderBy($request->input('orderby')['name'], $request->input('orderby')['sort']);
        } else {
            $results = $results->orderBy('created_at', 'desc');
        }

        if ($request->input('filter')) {
            foreach ($request->input('filter') as $key => $filter) {
                if ($key == 'status' && $filter['selected'] != '') {
                    if ($filter['selected'] == 'confirmed') {
                        $results = $results->where('patients.confirmed', 1);
                    }
                    if ($filter['selected'] == 'unconfirmed') {
                        $results = $results->where('patients.confirmed', 0);
                    }
                    if ($filter['selected'] == 'archived') {
                        $results = $results->where('patients.archived', 1);
                    }
                    if ($filter['selected'] == 'reset') {
                        $results = $results->where('patients.archived', 0);
                    }
                    if ($filter['selected'] == 'requested_deletation') {
                        $results = $results->whereHas('patientmeta', function($q) {
                            $q->whereNotNull('requested_deletation_at');
                        });
                    }
                }
                if ($key == 'lab' && $filter['selected'] != '' && $filter['selected'] != 'reset') {
                    if ($filter['selected'] == 'ohne') {
                        $results = $results->where('patients.lab_id', null);
                    } elseif ($filter['selected'] == 'current') {
                        $results = $results->where('patients.lab_id', $user->lab->first()->id);
                    } else {
                        $results = $results->where('patients.lab_id', $filter['selected']);
                    }
                }
                if ($key == 'phase' && $filter['selected'] != '' && $filter['selected'] != 'reset') {
                    $results = $results->where('patients.phase', intval($filter['selected']));
                }
                if ($key == 'queued' && $filter['selected'] != '' && $filter['selected'] != 'reset') {
                    $results = $results->where('patients.queued', intval($filter['selected']));
                }
                if ($key == 'documents' && $filter['selected'] != '' && $filter['selected'] != 'reset') {
                    if ($filter['selected'] == '0') {
                        $results = $results->whereDoesntHave('attachments');
                    }
                    if ($filter['selected'] == '1') {
                        $results = $results->has('attachments');
                    }
                }
            }
        }

        if ($request->input('searchfor')) {
            /*foreach($searchThrough as $item){
             $results=$results->orWhere($item,'LIKE','%'.$request->input('searchfor').'%');
            }*/
            $name = $request->input('searchfor');

            $results->where(function ($q) use ($name) {
                $q->where('patientmeta.name', 'like', "%{$name}%")
                    ->orWhere('patientmeta.tel', 'like', "%{$name}%")
                    ->orWhere('patientmeta.city', 'like', "%{$name}%")
                    ->orWhere('patientmeta.mobile', 'like', "%{$name}%");
            });
            //$results = $results->search($request->input('searchfor'), $searchThrough);
        } else {
            if ($user->hasRole('user')) {
                $results = $results->where('patients.queued', '1');
            }
        }

        if ($user->hasRole('admin')) {
            $results = $results->with('attachments');
        }
        if ($user->hasRole('lab')) {
            $lab = $user->lab->first();
            if (!$lab) {
                $lab = $user->labs()->first();
            }
            $results = $results->where('patients.queued', '0');
            $results = $results->where('patients.lab_id', $lab->id);
            // Überprüfe ob unbestätigte älter als 24 stunden sind und wähle nur die, die älter als 48 stunden sind
            $results = $results->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('patients.confirmed', 0)->where('patients.created_at', '<', \Carbon\Carbon::now()->subHours(24));
                })->orWhere('patients.confirmed', 1);
            });
            /*$results = $results->where([
                [DB::raw('IF(confirmed = 0 AND DATEDIFF(CURDATE(), patients.created_at) <= 1 , \'yeah\', \'no\')'), '=', 'no'],
            ]);*/
        }

        if ($user->hasRole('crm-user')) {
            $lab = $user->lab->first();
            if (!$lab) {
                $lab = $user->labs()->first();
            }
        }

        $results = $results->with(['patientmeta', 'lab', 'next_date', 'next_employee_date']);
        $results = $results->groupBy('patients.id');
        $results = $results->paginate($per_page);

        if ($user->hasRole('lab') || $user->hasRole('crm-user') || $request->filter['lab']['selected'] === 'current') {
            $all = Patient::where('patients.lab_id', $lab->id)->count();
        } else {
            $all = Patient::count();
        }

        $confirmed = Patient::where('confirmed', 1);
        if ($user->hasRole('lab') || $user->hasRole('crm-user') || $request->filter['lab']['selected'] === 'current') {
            $confirmed = $confirmed->where('patients.lab_id', $lab->id)->count();
        } else {
            $confirmed = $confirmed->count();
        }

        $response = [
            'pagination' => [
                'total'        => $results->total(),
                'per_page'     => $results->perPage(),
                'current_page' => $results->currentPage(),
                'last_page'    => $results->lastPage(),
                'from'         => $results->firstItem(),
                'to'           => $results->lastItem(),
            ],
            'filter'     => $request->input('filter'),
            'data'       => $results,
            'stats'      => [
                'all'       => $all,
                'confirmed' => $confirmed,
            ],
            'orderby'    => [
                'name' => $request->input('orderby')['name'],
                'sort' => $request->input('orderby')['sort'],
            ],
        ];

        return $response;
    }

    public function latestContacts()
    {
        if (Auth::user()->hasRole('admin')) {
            return Patient::with(['patientmeta', 'lab', 'nextDate'])->where('archived', '=', '0')->orderBy('created_at', 'DESC')->take(5)->get();
        }
        if (Auth::user()->hasRole('user')) {
            return Patient::with(['patientmeta', 'lab', 'nextDate'])->where('archived', '=', '0')->where('queued', '1')->orderBy('created_at', 'DESC')->take(5)->get();
        }

        $contacts       = [];
        $latestContacts = [];

        $labs = auth()->user()->lab->count() ? auth()->user()->lab : auth()->user()->labs;

        foreach ($labs as $lab) {
            $cons = Patient::with(['patientmeta', 'lab', 'dates'])
                ->where('lab_id', '=', $lab->id)
                ->where('queued', '=', '0')
                ->where('archived', '=', '0')
                ->orderBy('created_at', 'DESC');

            if (Auth::user()->hasRole('lab')) {
                $cons = $cons->where([
                    [DB::raw('IF(confirmed = 0 AND DATEDIFF(CURDATE(), patients.created_at) <= 1 , \'yeah\', \'no\')'), '=', 'no'],
                ]);
            }

            $cons = $cons->take(5)->get();
            if (sizeOf($cons) > 0) {
                $contacts [] = $cons;
            }
        }

        foreach ($contacts as $con) {
            foreach ($con as $c) {
                if ($c->queued == 0) {
                    $latestContacts[] = $c;
                }
            }
        }

        return $latestContacts;
    }

    public function contactStats()
    {
        $cons = Patient::where('created_at', '>', Carbon::now()->subDays(30))->count();
        $labs = Lab::where('status', '=', 'aktiv')->count();

        return [$labs, round($cons / $labs, 2)];
    }

    public function deleteContact(Request $request)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403);

        $patient = Patient::with('patientmeta')->find($request->id);

        if(!$patient) {
            return response()->json('Kontakt nicht vorhanden.');
        }

        Event::fire(new PatientDeleted($patient));

        if($request->delete_all && $patient->patientmeta) {
            $patients = Patient::whereHas('patientmeta', function($q) use ($patient) {
                $q->where('email', $patient->patientmeta->email)->where('patient_id', '<>', $patient->id);
            })->get();

            foreach ($patients as $p) {
                $p->forceDelete();
            }
        }

        if($request->send_mail) {
            mailer('AccountDeleted', $patient)->toPatient()->withoutDeleteLink()->send();
        }

        if($patient->requested_deletation_at) {
            $patient->forceDelete();
        } else {
            $patient->delete();
        }
    }

    public function movebackContact(Request $request)
    {
        $patient            = Patient::find($request['id']);
        $patient->queued    = 1;
        $patient->movedback = $patient->movedback + 1;
        $patient->save();

        activity()->causedBy($request->user())->performedOn($patient)->withProperty('type', $request->type)->log('queued');

        $data = [
            'patient' => $patient,
            'type'    => $request->input('type'),
        ];
        if ($request->input('type') != 'nodate') {
            Event::fire(new PatientMovedBack([$patient, $request->input('type')]));
        }
    }

    public function sendReminder(Request $request)
    {
        $patient = Patient::with('patientmeta')->find($request->patient_id);

        $email = Email::where('name', '=', 'UploadAttachments')->first();

        $message = Helper::markdown($email->body, null, $patient, null, $patient->token);
        $footer  = $email->footer;
        $subject = $email->subject;

        if ($patient->patientmeta->email) {
            Mail::send('emails.upload-attachments',
                ['body' => $message, 'footer' => $footer], function ($m) use ($patient, $subject) {
                    $m->from('buero@padento.de', 'Padento');
                    $m->to($patient->patientmeta->email, $patient->patientmeta->name)
                        ->subject($subject);
                });

            activity()->performedOn($patient)->withProperties(['subject' => $subject, 'mail' => $email])->log('mail_sent');
        }

        return response("success");
    }

    public function singleContact($id)
    {
        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('user') || $user->hasRole('crm-user')) {
            $patient = Patient::with([
                'patientmeta',
                'confirmer',
                'notes.user.lab.labmeta',
                'latestDate',
                'lab.dates', 'lab.labmeta', 'lab.user', 'dates', 'nextDate',
                'attachments',
                'tasks',
            ])->where('id', '=', $id)->first();

            return $patient;
        }
        if ($user->hasRole('lab')) {
            if ($user->lab->count()) {
                $lab = $user->lab->first();
            } else {
                $lab = $user->labs->first();
            }

            if(!$lab) {
                return ['error' => 'true'];
            }

            $lab_id = $lab->id;

            $patient = Patient::with(['patientmeta', 'latestDate', 'lab.dates', 'lab.labmeta', 'dates', 'nextDate', 'attachments', 'tasks'])
                ->with('notes.user.lab.labmeta')
                ->with([
                    'props' => function ($query) {
                        $query->where('patient_patient_prop.status', '=', 'Aktiv');
                    }])
                ->where('id', '=', $id)
                ->where('lab_id', $lab_id)
                ->first();

            if ($patient != '') {
                return $patient;
            } else {
                return ['error' => 'nicht deiner'];
            }
        }

        return ['error' => 'true'];
    }

    public function nextPatientDate($id)
    {
        $user = auth()->user();

        $patient = Patient::find($id);

        $nextDate = $patient->dates()->where('date', '>', Carbon::now())->orderBy('date', 'ASC')->first();

        if ($user->hasRole('admin') || $user->hasRole('user') || $user->hasRole('crm-user')) {
            return $nextDate;
        }
        if ($user->lab->id == $patient->lab_id) {
            return $nextDate;
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function updateContact(Request $request, $id)
    {
        $contact = Patient::find($id);

        if ($request['deletelabdate'] != '') {
            $formatedDate = date("Y-m-d H:i", strtotime($request['labdate']));
            $date         = Date::where('patient_id', $id)->where('date', $formatedDate)->orderBy('created_at', 'desc')->first();
            $date->delete();
        } else {
            if ($request['labdate'] != '') {
                $formatedDate = date("Y-m-d H:i", strtotime($request['labdate']));
                $date         = Date::where('patient_id', $id)->orderBy('created_at', 'desc')->first();
                if ($date != '') {
                    $date->date = $formatedDate;
                } else {
                    $date             = new Date();
                    $date->patient_id = $id;
                    $date->user_id    = Auth::user()->id;
                    $date->text       = '';
                    $date->date       = $formatedDate;
                    $date->lab_id     = $contact->lab->id;
                }
                $date->save();
            }
        }

        if ($contact->confirmed == 0 && $request->confirmed == 1) {
            if ($contact->queued == 0) {
                Event::fire(new PatientHasBeenApproved($contact));
            }

            activity()->causedBy($request->user())->performedOn($contact)->log('patient_confirmed');
        }

        $contact->confirmed = $request->confirmed;
        if ($request->confirmed_by != null) {
            $contact->confirmed_by = $request->confirmed_by;
        }

        if ($request->archived == '0') {
            $contact->unarchive();
        }
        if ($request->archived == '1') {
            $contact->archive();
            $contact->unqueue();
        }

        $contact->save();

        $meta = $contact->patientmeta;

        if ($request['patientmeta']['tel'] != '-' && $request['patientmeta']['tel'] != '') {
            // $meta->tel = phone_format($request['patientmeta']['tel'], $country_code = 'DE');
            $meta->tel = $request['patientmeta']['tel'];
        }
        if ($request['patientmeta']['mobile'] != '-' && $request['patientmeta']['mobile'] != '') {
            // $meta->mobile = phone_format($request['patientmeta']['mobile'], $country_code = 'DE');
            $meta->mobile = $request['patientmeta']['mobile'];
        }
        $meta->salutation    = $request['patientmeta']['salutation'];
        $meta->name          = $request['patientmeta']['name'];
        $meta->zip           = $request['patientmeta']['zip'];
        $meta->email         = $request['patientmeta']['email'];
        $meta->street        = $request['patientmeta']['street'];
        $meta->city          = $request['patientmeta']['city'];
        $meta->has_dentist   = $request['patientmeta']['has_dentist'];
        $meta->has_fear      = $request['patientmeta']['has_fear'];
        $meta->has_sdi       = $request['patientmeta']['has_sdi'];
        $meta->is_satisfied  = $request['patientmeta']['is_satisfied'];
        $meta->has_financing = $request['patientmeta']['has_financing'];
        $meta->has_tacs      = $request['patientmeta']['has_tacs'];
        $meta->insurance     = $request['patientmeta']['insurance'];

        $meta->save();

        return $contact;
    }

    public function saveDate(Request $request, $id)
    {
        $user = $request->user();

        $patient          = Patient::find($id);
        $date             = new Date();
        $lab              = Lab::find($patient->lab_id);
        $date->user_id    = Auth::user()->id;
        $date->lab_id     = $patient->lab_id;
        $date->patient_id = $patient->id;
        $date->date       = $request->input('datum');
        $date->end        = $request->input('end');
        $date->phase      = $date->patient->phase;

        if ($user->hasRole('admin') || $user->hasRole('user')) {
            $date->phase    = 3;
            $patient->phase = 3;
            $patient->save();
        }

        if ($request->has('text')) {
            $date->text = $request['text'];
        } else {
            $date->text = '...';
        }
        $date->save();

//        if ($patient->phase == 3) {
//            $patient->removeContactDates([$date->id]);
//        }

        if ($patient->queued == 1) {
            $patient->confirmed = 1;
//            add compfrmed by

            $patient->unqueue();
            $patient->save();
        }

        if ($patient->phase == 3) {
//            if ($user->hasRole('admin') || $user->hasRole('user')) {
            $patient->removeContactDates([$date->id]);
//            }
            Event::fire(new NewPatientDate($date));
        }

        activity()->causedBy($request->user())->performedOn($patient)->withProperties(['date' => $date->date, 'phase' => $date->phase])->log('date_added');
        activity()->causedBy($request->user())->performedOn($lab)->withProperties(['date' => $date->date, 'phase' => $date->phase])->log('date_added');

        return "success – date stored";
    }

    public function usedContacts(Request $request)
    {
        $usedPatient = PatientsUsed::with(['user'])->where(['patient_id' => $request->input('patient_id')])->first();

        if ($usedPatient != '' && $request->input('reset') != '') {
            $usedPatient->delete();

            return ['PatiendUsed deleted'];
        }
        if ($usedPatient == '' && $request->input('user_id') != '' && $request->input('patient_id') != '') {
            $usedPatient = PatientsUsed::create(['patient_id' => $request->input('patient_id'), 'user_id' => $request->input('user_id')]);

            return $usedPatient;
        } else {
            if ($request->input('user_id') != '') {
                if ($usedPatient) {
                    if ($request->input('user_id') != $usedPatient->user_id) {
                        $timetowait = Settings::where('name', '=', 'Nach wie vielen Minuten soll ein Kontakt wieder freigegeben werden?')->first()->value;
                        if ($timetowait - ((new \Carbon\Carbon($usedPatient->updated_at))->diffInMinutes()) < 0) {
                            $usedPatient->delete();
                            $usedPatient = PatientsUsed::create(['patient_id' => $request->input('patient_id'), 'user_id' => $request->input('user_id')]);
                        }

                        return $usedPatient;
                    } else {
                        $usedPatient->updated_at = date('Y-m-d H:i:s');
                        $usedPatient->save();

                        return $usedPatient;
                    }
                }
            }
        }
        if (!$usedPatient) {
            return ['PatiendUsed is not available'];
        }
    }

    public function refer($lab, $contact, $refertype = '')
    {
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('user')) {
            $patient = Patient::find($contact);
            $lab     = Lab::find($lab);

            if ($refertype == 'notavailable') {
                if ($lab->membership == 1 || $lab->membership == 4) {
                    $patient->membership = 1;
                } else {
                    $patient->membership = 0;
                }
                $patient->unqueue();
                $patient->phase  = 2;
                $patient->lab_id = $lab->id;

                $patient->save();
                Event::fire(new PatientReferred($patient));
                Event::fire(new PatientUnobtainable($patient, $lab));

                $patient->removeDates();
            }
            if ($refertype == 'selfcontact') {
                $patient->membership = 0;
                $patient->unqueue();
                $patient->phase     = 2;
                $patient->confirmed = 1;
                $patient->lab_id    = $lab->id;

                $patient->save();
                Event::fire(new PatientReferred($patient));
                Event::fire(new PatientSelfContacted($patient));

                $patient->removeDates();
            }
            if ($refertype == 'justmove') {
                $patient->moveToLab($lab);

                if (!$patient->lab->isQueueLab() && $patient->confirmed) {
                    Event::fire(new PatientReferred($patient));
                }
            }

            activity()->causedBy(auth()->user())->performedOn($patient)
                ->withProperties(['referType' => $refertype, 'lab' => $lab])->log('moved_back');

            return "Success. Patient successfully referred to lab.";
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function saveNote(Request $request, $id)
    {
        $patient = Patient::find($id);

        $note             = new PatientNote();
        $note->user_id    = Auth::user()->id;
        $note->patient_id = $id;
        $note->msg        = '<p>' . str_replace("\n", "<br/>", $request->note);

        $patient->notes()->save($note);

        activity()->causedBy($request->user())->performedOn($patient)->withProperties(['note' => $note->msg])->log('note_added');

        return "note saved";
    }

    public function index(Request $request)
    {

        // if ($request->input('pagination')['per_page']) {
        //     $per_page = $request->input('pagination')['per_page'];
        // } else {
        //     $per_page = 100;
        // }

        // $searchThrough = [
        //     'patientmeta.name',
        //     'patientmeta.email',
        //     'patientmeta.tel',
        //     'patientmeta.zip',
        //     'created_at',
        //     'id',
        // ];

        // //SELECT patients.*, MIN(dates.date) FROM patients LEFT JOIN dates ON patients.id = dates.patient_id AND dates.date > NOW() GROUP BY patients.id;
        // $results = Patient::select([
        //     'patients.*',
        //     DB::raw('MIN(dates.date) as labDate'),
        //     DB::raw('MIN(employee_dates.date) as empDate'),

        // ]);
        // $results = $results->leftJoin('dates', function($query) {
        //     $query->on('patients.id', '=', 'dates.patient_id');
        //     $query->on('dates.date', '>', DB::raw('NOW()'));
        // });
        // $results = $results->leftJoin('employee_dates', function($query) {
        //     $query->on('patients.id', '=', 'employee_dates.patient_id');
        //     $query->on('employee_dates.date', '>', DB::raw('NOW()'));
        // });

        // if ($request->input('orderby')) {
        //     $results = $results->orderBy($request->input('orderby')['name'], $request->input('orderby')['sort']);
        // } else {
        //     $results = $results->orderBy('created_at', 'desc');
        // }

        // if ($request->input('searchfor')) {
        //     $results = $results->search($request->input('searchfor')['value'], $searchThrough);
        // } else {
        //     if (Auth::user()->hasRole('user')) {
        //         $results = $results->where('patients.queued', '1');
        //     }
        // }

        // if (Auth::user()->hasRole('lab')) {
        //     $lab = Auth::user()->lab->first();
        //     $results = $results->where('patients.queued', '0');
        //     $results = $results->where('patients.lab_id', $lab->id);
        //     // Überprüfe ob unbestätigte älter als 24 stunden sind und wähle nur die, die älter als 24 stunden sind
        //     $results = $results->where([
        //         [DB::raw('IF(confirmed = 0 AND DATEDIFF(CURDATE(), patients.created_at) <= 1 , \'yeah\', \'no\')'), '=', 'no'],
        //     ]);
        // }

        // $results = $results->with(['patientmeta', 'lab']);
        // $results = $results->groupBy('patients.id');
        // $results = $results->paginate($per_page);

        // $all = Patient::with(['patientmeta', 'lab', 'nextDate', 'lastDate']);
        // if (Auth::user()->hasRole('lab')) {
        //     $all = $all->where('patients.lab_id', $lab->id)->count();
        // } else {
        //     $all = $all->count();
        // }

        // $confirmed = Patient::with(['patientmeta', 'lab', 'nextDate'])->where('confirmed', 1);
        // if (Auth::user()->hasRole('lab')) {
        //     $confirmed = $confirmed->where('patients.lab_id', $lab->id)->count();
        // } else {
        //     $confirmed = $confirmed->count();
        // }

        // $response = [
        //     'pagination' => [
        //         'total' => $results->total(),
        //         'per_page' => $results->perPage(),
        //         'current_page' => $results->currentPage(),
        //         'last_page' => $results->lastPage(),
        //         'from' => $results->firstItem(),
        //         'to' => $results->lastItem()
        //     ],
        //     'filter' => $request->input('filter'),
        //     'data' => $results,
        //     'stats' => [
        //         'all' => $all,
        //         'confirmed' => $confirmed,
        //     ],
        //     'orderby' => [
        //         'name' => $request->input('orderby')['name'],
        //         'sort' => $request->input('orderby')['sort'],
        //     ],
        // ];
        // return $response;

    }
}
