<?php

namespace App\Http\Controllers\API;

use App\Role;
use Uuid;
use Auth;
use Event;
use App\Lab;
use App\User;
use Activity;
use App\LabMeta;
use App\Patient;
use Carbon\Carbon;
use App\LabSetting;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Events\LabMembershipChange;
use App\Http\Controllers\Controller;

class LabController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('user')) {
            if ($request->input('pagination')) {
                $per_page = $request->input('pagination')['per_page'];
            } else {
                $per_page = 200;
            }

            $searchThrough = [
                'name',
                'labmeta.city',
                'labmeta.zip',
            ];

            $results = Lab::join('lab_metas as labmeta', 'labmeta.lab_id', '=', 'labs.id');
            if ($request->input('orderby')) {
                $results = $results->orderBy($request->input('orderby')['name'], $request->input('orderby')['sort']);
            } else {
                $results = $results->orderBy('labs.name', 'asc');
            }
            if ($request->input('filter')) {
                // return $request->input('filter');
                foreach ($request->input('filter') as $key => $filter) {
                    // return [$key => $filter];
                    if ($key != 'isCRM' && $key != 'isDentistCRM' /*&& $key != 'isMultipleUser'*/) {
                        if ($filter['selected'] != 'reset') {
                            // return [$key, $filter['selected']];
                            $results = $results->where($key, $filter['selected']);
                        }
                    } else {
                        //todo: fitler on CRM role for lab user
                        if ($key == 'isCRM' && $filter == 'true') {
                            $results = $results->whereHas('user', function ($q) {
                                $q->whereHas('roles', function ($q) {

                                    $q->where('name', 'crm-user');
                                });
                            });
                        }
                        if ($key == 'isDentistCRM' && $filter == 'true') {
                            $results = $results->whereHas('user', function ($q) {
                                $q->whereHas('roles', function ($q) {

                                    $q->where('name', 'dentist-crm-user');
                                });
                            });
                        }
                        /*if ($key == 'isMultipleUser' && $filter == 'true') {
                            $results = $results->where('has_multi_user', true);
                        }*/
                    }
                }
            } else {
                if ($request->has('all') && $request->all) {

                } else {
                    $results->where('labs.status', '=', 'aktiv');
                }
            }
            if ($request->input('searchfor')) {
                $results = $results->search($request->input('searchfor'), $searchThrough);
            }

            if ($request->input('crmuser')) {

            }
            $results  = $results->select('labs.*');
            $results  = $results->with(['user', 'labmeta', 'allPatientCount', 'thirtyDaysPatientCount', 'sevenDaysPatientCount']);
            $results  = $results->paginate($per_page);
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
                    'countLabs'                  => Lab::select('id')->where('status', '=', 'aktiv')->count(),
                    'countContactPastThirtyDays' => Patient::select('id')->where('created_at', '>', Carbon::now()->subDays(30))->count(),
                ],
                'orderby'    => [
                    'name' => $request->input('orderby')['name'],
                    'sort' => $request->input('orderby')['sort'],
                ],
            ];

            return $response;
        }

        return 'DU NICHT!';
    }

    public function show($id)
    {
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('user')) {
            $lab = Lab::with('labmeta')->with('patients')->with(['user', 'users'])->where('id', '=', $id)->first();
            if (!$lab) {
                $lab         = Lab::with('labmeta')->with('patients')->with(['users'])->whereHas('users', function ($query) {
                    $query->where('user_id', Auth::user()->id);
                })->first();
                $lab['user'] = User::find(Auth::user()->id);
            }

            return $lab;
        } else {
            $lab = Lab::with('labmeta')->with('patients')->with(['user', 'users'])->where('user_id', '=', Auth::user()->id)->first();
            if (!$lab) {
                $lab         = Lab::with('labmeta')->with('patients')->with(['users'])->whereHas('users', function ($query) {
                    $query->where('user_id', Auth::user()->id);
                })->first();
                $lab['user'] = User::find(Auth::user()->id);
            }

            return $lab;
        }
    }

    public function store(Request $request, $id)
    {
        $this->validate($request, [
            'slug' => 'required|min:2|regex:/^[A-Za-z-_ 0-9\.\-\/]+$/|unique:labs,slug,' . $id,
        ]);

        $lab = Lab::find($id);

        if ($lab->user_id == Auth::user()->id || Auth::user()->hasRole('admin')) {
            if ($request['membership'] == '1' || $request['membership'] == '4') {
                $default_duration = LabSetting::firstOrCreate(['name' => 'default_date_duration', 'lab_id' => $id]);
                if ($default_duration->value == '') {
                    $default_duration->value = '45';
                    $default_duration->save();
                }
            }
            if ($lab->membership != $request['membership']) {
                Event::fire(new LabMembershipChange($lab));
            }
            $lab->membership = $request['membership'];
            if ($request['status'] == 'aktiv') {
                $lab->status = 'aktiv';
            } else {
                $lab->status = 'inaktiv';
            }

            if ($request->get('name')) {
                $labName = $request->name;

                $lab->name = $labName;
            }

            if($slug = $request->get('slug')) {
                $lab->slug = $slug;
            }

            $lab->save();

            $lab->saveSlug();

            $user = User::find($lab->user_id);

            if ($request->get('user')) {
                $user->name = $request->get('user')['name'];
            }

            if ($request['status'] == 'aktiv') {
                $user->status = 'Aktiv';
            } else {
                $user->status = 'Deaktiviert';
            }
            if ($request['user']['allowlogin'] == 'allow') {
                $user->allowlogin = 'allow';
            }
            if ($request['user']['allowlogin'] == 'disallow') {
                $user->allowlogin = 'disallow';
            }
            $user->save();

            if ($lab->labmeta) {
//                if ($lab->labmeta->zip != $request['labmeta']['zip']) {
                $geo = getLocation($lab->labmeta->zip, $lab->labmeta->country_code);
                if (array_get($geo, 'latitude') && array_get($geo, 'longitude')) {
                    $lab->lat = $geo['latitude'];
                    $lab->lon = $geo['longitude'];
                    $lab->save();
                }
//            }
                $meta = $lab->labmeta->update($request['labmeta']);
            } else {
                // $labmeta = new Labmeta::create();
                $meta = $lab->labmeta()->create($request['labmeta']);
            }

            return "lab saved";
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function destroy($id)
    {
        $lab = Lab::findOrFail($id);

        $lab->delete();

        return response()->json(['status' => '200', 'message' => 'lab removed successfully.']);
    }

    public function newLab(Request $request)
    {
        $labdata = $request['account'];

        $user             = new User();
        $user->email      = $labdata['user']['email'];
        $user->name       = $labdata['user']['name'];
        $user->allowlogin = 'allow';
        $user->password   = bcrypt($labdata['user']['password']);
        $user->save();

        $user->roles()->attach('3');

        $lab = new Lab();

        $lab->name   = $labdata['lab']['name'];
        $lab->status = 'inaktiv';

        $lab->user_id     = $user->id;
        $lab->google_city = '';
        $lab->directtoken = Uuid::generate('4');
        $lab->slug        = str_slug($labdata['lab']['name'], '-');
        $lab->lat         = '';
        $lab->lon         = '';

        $lab->save();

        $labmeta = new LabMeta();

        if (isset($labdata['labmeta']['hello'])) {
            $labmeta->hello = $labdata['labmeta']['hello'];
        }

        if (isset($labdata['labmeta']['contact_person'])) {
            $labmeta->contact_person = $labdata['labmeta']['contact_person'];
        }

        if (isset($labdata['labmeta']['special1'])) {
            $labmeta->special1 = $labdata['labmeta']['special1'];
        }

        if (isset($labdata['labmeta']['special2'])) {
            $labmeta->special2 = $labdata['labmeta']['special2'];
        }

        if (isset($labdata['labmeta']['special3'])) {
            $labmeta->special3 = $labdata['labmeta']['special3'];
        }

        if (isset($labdata['labmeta']['special4'])) {
            $labmeta->special4 = $labdata['labmeta']['special4'];
        }

        if (isset($labdata['labmeta']['special5'])) {
            $labmeta->special5 = $labdata['labmeta']['special5'];
        }

        if (isset($labdata['labmeta']['text'])) {
            $labmeta->text = $labdata['labmeta']['text'];
        }

        if (isset($labdata['labmeta']['contact_email'])) {
            $labmeta->contact_email = $labdata['labmeta']['contact_email'];
        }

        if (isset($labdata['labmeta']['tel'])) {
            $labmeta->tel = phone_format($labdata['labmeta']['tel'], $country_code = 'DE');
        }

        if (isset($labdata['labmeta']['street'])) {
            $labmeta->street = $labdata['labmeta']['street'];
        }

        if (isset($labdata['labmeta']['city'])) {
            $labmeta->city = $labdata['labmeta']['city'];
        }

        if (isset($labdata['labmeta']['zip'])) {
            $labmeta->zip = $labdata['labmeta']['zip'];
        }

        // $labmeta->url = $labdata['labmeta']['url'];

        $lab->labmeta()->save($labmeta);

        return [
            'data'   => [
                'lab_id' => $lab->id,
            ],
            'status' => 'success',
        ];
    }

    public function singleLabStats($id)
    {
        $lab = Lab::find($id);

        $all        = $lab->patients()->count();
        $month      = $lab->patients()->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()])->count();
        $thirtyDays = $lab->patients()->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])->count();
        $sevenDays  = $lab->patients()->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $user_id    = $lab->user->id;
//        $logins = Activity::with('user')->where('user_id', '=', $user_id)->where('text', 'LIKE', '%angemeldet%')->latest()->limit(100)->count();
        $logins = 0;

        $dates = $lab->dates()->count();
        foreach (Auth::user()->labs as $l) {
            if ($l->id == $lab->id) {
                return response()->json(['all' => $all, 'month' => $month, 'thirtydays' => $thirtyDays, 'sevendays' => $sevenDays, 'logins' => $logins, 'dates' => $dates]);
            }
        };

        if (Auth::user()->hasRole('admin')) {
            return response()->json(['all' => $all, 'month' => $month, 'thirtydays' => $thirtyDays, 'sevendays' => $sevenDays, 'logins' => $logins, 'dates' => $dates]);
        } else {
            if ($lab->user_id == Auth::user()->id) {
                return response()->json(['all' => $all, 'month' => $month, 'thirtydays' => $thirtyDays, 'sevendays' => $sevenDays, 'logins' => $logins, 'dates' => $dates]);
            }
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function allocateLab($user, $labid)
    {
        $user = User::find($user);
        $lab  = Lab::find($labid);

        $user->labs()->attach($lab);

        return "lab allocated";

        return [$user, $lab];
    }

    public function images($labId)
    {
        return Lab::findOrFail($labId)->images->groupBy('type');
    }

    public function checkCrmAccess($labId)
    {
        $labUser = Lab::findOrFail($labId)->user;
        if (!$labUser) {
            $isMainLabUser = Lab::findOrFail($labId)->user == User::find(Auth::user()->id);
            if ($isMainLabUser) {
                $labUser = Lab::findOrFail($labId)->user;
            } else {
                $labUser = Lab::findOrFail($labId)->users()->where('user_id', Auth::user()->id)->first();
            }
        }

        return [
            'authorized' => $labUser->hasRole('crm-user'),
        ];
    }

    public function toggleCrmAccess($labId)
    {
        $lab     = Lab::findOrFail($labId);
        $labUser = $lab->user;

        $crmUserRole = Role::where('name', 'crm-user')->first();

        if ($labUser->hasRole('crm-user')) {
            $labUser->roles()->detach($crmUserRole);
            $lab->users->each(function ($user) use ($crmUserRole) {
                $user->roles()->detach($crmUserRole);
            });
        } else {
            $labUser->roles()->attach($crmUserRole);
            $lab->users->each(function ($user) use ($crmUserRole) {
                $user->roles()->detach($crmUserRole);
            });
        }

        return [
            'authorized' => $labUser->fresh()->hasRole('crm-user'),
        ];
    }

    public function checkDentistCrmAccess($labId)
    {
        $labUser = Lab::findOrFail($labId)->user;
        if (!$labUser) {
            $isMainLabUser = Lab::findOrFail($labId)->user == User::find(Auth::user()->id);
            if ($isMainLabUser) {
                $labUser = Lab::findOrFail($labId)->user;
            } else {
                $labUser = Lab::findOrFail($labId)->users()->where('user_id', Auth::user()->id)->first();
            }
        }

        return [
            'authorized' => $labUser->hasRole('dentist-crm-user'),
        ];
    }

    public function toggleDentistCrmAccess($labId)
    {
        $lab = Lab::findOrFail($labId);

        $labUser = $lab->user;

        $dentistCrmUserRole = Role::where('name', 'dentist-crm-user')->first();

        if ($labUser->hasRole('dentist-crm-user')) {
            $labUser->roles()->detach($dentistCrmUserRole);
            $lab->users->each(function ($user) use ($dentistCrmUserRole) {
                $user->roles()->detach($dentistCrmUserRole);
            });
        } else {
            $labUser->roles()->attach($dentistCrmUserRole);
            $lab->users->each(function ($user) use ($dentistCrmUserRole) {
                $user->roles()->attach($dentistCrmUserRole);
            });
        }

        return [
            'authorized' => $labUser->fresh()->hasRole('dentist-crm-user'),
        ];
    }

    public function checkHasExtraUsers($labId)
    {
        $lab = Lab::findOrFail($labId);

        return $lab->has_multi_user ? ['authorized' => true] : ['authorized' => false];
    }

    public function toggleHasExtraUsers($labId)
    {
        $lab = Lab::findOrFail($labId);

        if ($lab->has_multi_user) {
            if ($lab->users()->exists()) {
                return ['authorized' => true];
            } else {
                $lab->update(['has_multi_user' => false]);

                return ['authorized' => false];
            }
        } else {
            $lab->update(['has_multi_user' => true]);

            return ['authorized' => true];
        }
    }

}
