<?php

namespace App\Http\Controllers\API;

use App\Date;
use App\Dentist;
use App\DentistsUsed;
use App\Events\PatientReferred;
use App\Events\PatientSelfContacted;
use App\Events\PatientUnobtainable;
use App\DentistNote;
use App\Settings;
use DB;
use Auth;
use Illuminate\Support\Facades\Event;
use Mail;
use Helper;
use App\Lab;
use App\DentistDate;
use App\Email;
use App\DentistContact;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Events\NewDentistDate;
use App\Events\DentistDeleted;
use App\Http\Controllers\Controller;
use App\Events\DentistHasBeenApproved;

class DentistContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function deleteDentist(Request $request)
    {
        if (Auth::user()->hasRole('admin')) {
            $dentist = DentistContact::with('dentistmeta')->find($request['id']);
            $temp    = $dentist;
            $dentist->delete();
            $dentist = $temp;
            Event::fire(new DentistDeleted($dentist));
        }
    }

    public function saveDate(Request $request, $id)
    {
        $dentist                  = DentistContact::find($id);
        $date                     = new Date();
        $lab                      = Lab::find($request->lab_id);
        $date->user_id            = Auth::user()->id;
        $date->lab_id             = $dentist->lab_id;
        $date->dentist_contact_id = $dentist->id;
        $date->date               = $request->input('datum');
        $date->end                = $request->input('end');
        $date->phase              = $dentist->phase;

        if ($request->has('text')) {
            $date->text = $request['text'];
        } else {
            $date->text = '...';
        }

        $date->save();

        $date = $date->load(['lab', 'patient', 'user', 'dentist_contact']);

//        Event::fire(new NewDentistDate($date));

        activity()->causedBy($request->user())->performedOn($dentist)->withProperties(['date' => $date->date])->log('date_added');

        // activity()->causedBy($request->user())->performedOn($lab)->withProperties(['date' => $date->date])->log('date_added');

        return "success – date stored";
    }

    public function alldentists(Request $request)
    {
        $user = $request->user();

        if ($request->input('pagination')['per_page']) {
            $per_page = $request->input('pagination')['per_page'];
        } else {
            $per_page = 100;
        }

        $searchThrough = [
            'dentistmeta.name',
            'dentistmeta.tel',
            'dentistmeta.mobile',
            'dentistmeta.city',
            // 'dentistmeta.zip',
            // 'created_at',
            'id',
        ];

        //SELECT patients.*, MIN(dates.date) FROM patients LEFT JOIN dates ON patients.id = dates.dentist_contact_id AND dates.date > NOW() GROUP BY patients.id;
        $results = DentistContact::select([
            'dentist_contacts.*',
            DB::raw('MIN(dates.date) as labDate'),
            DB::raw('MIN(employee_dates.date) as empDate'),
        ]);
        $results = $results->join('dentist_contact_metas as dentistmeta', 'dentistmeta.dentist_contact_id', '=', 'dentist_contacts.id');

        $results = $results->leftJoin('dates', function ($query) {
            $query->on('dates.dentist_contact_id', '=', 'dentist_contacts.id');
            $query->on('dates.phase', '=', 'dentist_contacts.phase');
            $query->whereNull('dates.deleted_at');
            // $query->on('dentist_dates.date', '>', DB::raw('NOW()'));
        });
        $results = $results->leftJoin('employee_dates', function ($query) {
            $query->on('dentist_contacts.id', '=', 'employee_dates.dentist_contact_id');
            $query->whereNull('employee_dates.deleted_at');
            // $query->on('employee_dates.date', '>', DB::raw('NOW()'));
        });

        /*
        $results = $results->leftJoin('labs', function ($query) {
            $query->on('dentist_contacts.lab_id', '=', 'labs.id');
        });
        */

        if ($request->input('orderby')) {
            $results = $results->orderBy($request->input('orderby')['name'], $request->input('orderby')['sort']);
        } else {
            $results = $results->orderBy('dentist_contacts.created_at', 'desc');
        }

        if ($request->input('filter')) {
            foreach ($request->input('filter') as $key => $filter) {
                if ($key == 'status' && $filter['selected'] != '') {
                    if ($filter['selected'] == 'confirmed') {
                        $results = $results->where('dentist_contacts.confirmed', 1);
                    }
                    if ($filter['selected'] == 'unconfirmed') {
                        $results = $results->where('dentist_contacts.confirmed', 0);
                    }
                    if ($filter['selected'] == 'archived') {
                        $results = $results->where('dentist_contacts.archived', 1);
                    }
                    if ($filter['selected'] == 'reset') {
                        $results = $results->where('dentist_contacts.archived', 0);
                    }
                }
                if ($key == 'lab' && $filter['selected'] != '' && $filter['selected'] != 'reset') {
                    if ($filter['selected'] == 'ohne') {
                        $results = $results->where('dentist_contacts.lab_id', null);
                    } elseif ($filter['selected'] == 'current') {
                        $lab = $user->lab->first();

                        if (!$lab)
                            $lab = $user->labs->first();
                        $results = $results->where('dentist_contacts.lab_id', $lab->id);
                    } else {
                        $results = $results->where('dentist_contacts.lab_id', $filter['selected']);
                    }
                }
                if ($key == 'phase' && $filter['selected'] != '' && $filter['selected'] != 'reset') {
                    $results = $results->where('dentist_contacts.phase', intval($filter['selected']));
                }
                if ($key == 'queued' && $filter['selected'] != '' && $filter['selected'] != 'reset') {
                    $results = $results->where('dentist_contacts.queued', intval($filter['selected']));
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
            //$results = $results->search($request->input('searchfor'), $searchThrough);
            $name = $request->input('searchfor');
            $lab = null;
            if ($user->hasRole('lab')) {
                $lab = $user->lab->first();
                if (!$lab) {
                    $lab = $user->labs->first();
                }
            }
            if ($user->hasRole('crm-user')) {
                $lab = $user->lab->first();

                if (!$lab) {
                    $lab = $user->labs->first();
                }
            }
            $results = $results->where(function ($query) use ($lab, $name) {
                if ($lab) {
                    $query->where('dentist_contacts.lab_id', $lab->id);
                }
                $query->where('dentistmeta.mobile', 'like', "%{$name}%");
            })
                ->orWhere(function ($query) use ($lab, $name) {
                    if ($lab) {
                        $query->where('dentist_contacts.lab_id', $lab->id);
                    }
                    $query->where('dentistmeta.tel', 'like', "%{$name}%");
                })
                ->orWhere(function ($query) use ($lab, $name) {
                    if ($lab) {
                        $query->where('dentist_contacts.lab_id', $lab->id);
                    }
                    $query->where('dentistmeta.name', 'like', "%{$name}%");
                })
                ->orWhere(function ($query) use ($lab, $name) {
                    if ($lab) {
                        $query->where('dentist_contacts.lab_id', $lab->id);
                    }
                    $query->where('dentistmeta.city', 'like', "%{$name}%");
                });
        } else {
            /*  if ($user->hasRole('user')) {
                  $results = $results->where('dentist_contacts.queued', '1');
              }*/
        }

        if ($user->hasRole('admin')) {
            $results = $results->with('attachments');
        }

        if ($user->hasRole('lab')) {
            $results = $results->with('attachments');
            $lab     = $user->lab->first();

            if (!$lab) {
                $lab = $user->labs->first();
            }
            $results = $results->where('dentist_contacts.queued', '0');
            if (!$user->hasRole('admin')) {
                $results = $results->where('dentist_contacts.lab_id', $lab->id);
            }
            // Überprüfe ob unbestätigte älter als 24 stunden sind und wähle nur die, die älter als 48 stunden sind
            $results = $results->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('dentist_contacts.confirmed', 0)->where('dentist_contacts.created_at', '<', \Carbon\Carbon::now()->subHours(24));
                })->orWhere('dentist_contacts.confirmed', 1);
            });
        }

        if ($user->hasRole('crm-user')) {
            $lab = $user->lab->first();

            if (!$lab) {
                $lab = $user->labs->first();
            }
        }


        if ($user->hasRole('lab') || $user->hasRole('crm-user') || $request->filter['lab']['selected'] === 'current') {
            $all = DentistContact::where('dentist_contacts.lab_id', $lab->id)->count();
        } else {
            $all = DentistContact::count();
        }

        $results = $results->with(['dentistmeta', 'lab']);
        $results = $results->groupBy('dentist_contacts.id');
        $results = $results->paginate($per_page);
        $confirmed = DentistContact::where('confirmed', 1);
        if ($user->hasRole('lab') || $user->hasRole('crm-user') || $request->filter['lab']['selected'] === 'current') {
            $confirmed = $confirmed->where('dentist_contacts.lab_id', $lab->id)->count();
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

    public function singleDentist($id)
    {
        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('user') || $user->hasRole('crm-user')) {
            $dentist = DentistContact::with([
                'dentistmeta',
                'confirmer',
                'latestDate',
                'lab.user',
                'dates', 'nextDate',
                'attachments',
                'tasks',
                'notes.user',
            ])->where('id', '=', $id)->first();

            return $dentist;
        }
        if ($user->hasRole('lab')) {
            //$lab_id = $user->lab[0]->id;
            $lab = $user->lab->first();

            if (!$lab) {
                $lab = $user->labs->first();
            }
            $lab_id = $lab->id;

            $dentist = DentistContact::with(['dentistmeta', 'latestDate', 'dates', 'nextDate', 'attachments', 'tasks'])
                ->with('notes.user')
                /*          ->with([
                              'props' => function ($query) {
                                  $query->where('dentist_dentist_prop.status', '=', 'Aktiv');
                              }])
                */
                ->where('id', '=', $id)
                ->where('lab_id', $lab_id)
                ->first();

            if ($dentist != '') {
                return $dentist;
            } else {
                return ['error' => 'nicht deiner'];
            }
        }

        return ['error' => 'true'];
    }

    public function usedDentists(Request $request)
    {
        $usedDentist = DentistsUsed::with(['user'])->where(['dentist_contact_id' => $request->input('dentist_contact_id')])->first();
        if ($usedDentist != '' && $request->input('reset') != '') {
            $usedDentist->delete();

            return ['DentistUsed deleted'];
        }
        if ($usedDentist == '' && $request->input('user_id') != '') {
            $usedDentist = DentistsUsed::create(['dentist_contact_id' => $request->input('dentist_contact_id'), 'user_id' => $request->input('user_id')]);

            return $usedDentist;
        } else {
            if ($request->input('user_id') != '') {
                if ($request->input('user_id') != $usedDentist->user_id) {
                    $timetowait = Settings::where('name', '=', 'Nach wie vielen Minuten soll ein Kontakt wieder freigegeben werden?')->first()->value;
                    if ($timetowait - ((new \Carbon\Carbon($usedDentist->updated_at))->diffInMinutes()) < 0) {
                        $usedDentist->delete();
                        $usedDentist = DentistsUsed::create(['dentist_contact_id' => $request->input('dentist_contact_id'), 'user_id' => $request->input('user_id')]);
                    }

                    return $usedDentist;
                } else {
                    $usedDentist->updated_at = date('Y-m-d H:i:s');
                    $usedDentist->save();

                    return $usedDentist;
                }
            }
        }
    }

    public function saveNote(Request $request, $id)
    {
        $dentist = DentistContact::find($id);

        $note                     = new DentistNote();
        $note->user_id            = Auth::user()->id;
        $note->dentist_contact_id = $id;
        $note->msg                = '<p>' . str_replace("\n", "<br/>", $request->note);
        $dentist->notes()->save($note);

        activity()->causedBy($request->user())->performedOn($dentist)->withProperties(['note' => $note->msg])->log('note_added');

        return "note saved";
    }

    public function updateContact(Request $request, $id)
    {
        $dentist = DentistContact::find($id);

        if ($request['deletelabdate'] != '') {
            $formatedDate = date("Y-m-d H:i", strtotime($request['labdate']));
            $date         = DentistDate::where('dentist_contact_id', $id)->where('date', $formatedDate)->orderBy('created_at', 'desc')->first();
            $date->delete();
        } else {
            if ($request['labdate'] != '') {
                $formatedDate = date("Y-m-d H:i", strtotime($request['labdate']));
                $date         = DentistDate::where('dentist_contact_id', $id)->orderBy('created_at', 'desc')->first();
                if ($date != '') {
                    $date->date = $formatedDate;
                } else {
                    $date                     = new DentistDate();
                    $date->dentist_contact_id = $id;
                    $date->user_id            = Auth::user()->id;
                    $date->text               = '';
                    $date->lab_id             = $request['lab_id'];
                    $date->date               = $formatedDate;
                }
                $date->save();
            }
        }

        // $props = $dentist->props;

        if ($dentist->confirmed == 0 && $request->confirmed == 1) {
            if ($dentist->queued == 0) {
                Event::fire(new DentistHasBeenApproved($dentist));
            }

            activity()->causedBy($request->user())->performedOn($dentist)->log('dentist_confirmed');
        }

        $dentist->confirmed = $request->confirmed;
        if ($request->confirmed_by != null) {
            $dentist->confirmed_by = $request->confirmed_by;
        }

        if ($request->archived == '0') {
            $dentist->archived = 0;
        }
        if ($request->archived == '1') {
            $dentist->archived = 1;
        }

        $dentist->save();

        $meta = $dentist->dentistmeta;

        if (array_key_exists("tel", $request['dentistmeta'])) {
            if ($request['dentistmeta']['tel'] != '-' && $request['dentistmeta']['tel'] != '') {
                // $meta->tel = phone_format($request['dentistmeta']['tel'], $country_code = 'DE');
                $meta->tel = $request['dentistmeta']['tel'];
            }
        }
        if (array_key_exists("mobile", $request['dentistmeta']))
        {
            if ($request['dentistmeta']['mobile'] != '-' && $request['dentistmeta']['mobile'] != '')
            {
                // $meta->mobile = phone_format($request['dentistmeta']['mobile'], $country_code = 'DE');
                $meta->mobile = $request['dentistmeta']['mobile'];
            }
        }
        $meta->salutation    = $request['dentistmeta']['salutation'];
        $meta->name          = $request['dentistmeta']['name'];
        $meta->zip           = $request['dentistmeta']['zip'];
        $meta->email         = $request['dentistmeta']['email'];
        $meta->street        = $request['dentistmeta']['street'];
        $meta->city          = $request['dentistmeta']['city'];
        $meta->has_dentist   = $request['dentistmeta']['has_dentist'];
        $meta->has_fear      = $request['dentistmeta']['has_fear'];
        $meta->has_sdi       = $request['dentistmeta']['has_sdi'];
        $meta->is_satisfied  = $request['dentistmeta']['is_satisfied'];
        $meta->has_financing = $request['dentistmeta']['has_financing'];
        $meta->has_tacs      = $request['dentistmeta']['has_tacs'];
        $meta->insurance     = $request['dentistmeta']['insurance'];

        $meta->save();

        return $dentist;
    }

    public function sendReminder(Request $request)
    {
        $dentist = DentistContact::with('dentistmeta')->find($request->dentist_contact_id);

        $email = Email::where('name', '=', 'UploadAttachments')->first();

        $message = Helper::markdowndentist($email->body, null, $dentist, null, $dentist->token);
        $footer  = $email->footer;
        $subject = $email->subject;

        if ($dentist->dentistmeta->email) {
            Mail::send('emails.upload-attachments',
                ['body' => $message, 'footer' => $footer], function ($m) use ($dentist, $subject) {
                    $m->from('buero@padento.de', 'Padento');
                    $m->to($dentist->dentistmeta->email, $dentist->dentistmeta->name)
                        ->subject($subject);
                });
        }

        return response("success");
    }

}
