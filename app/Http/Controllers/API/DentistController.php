<?php

namespace App\Http\Controllers\API;

use Auth;
use Uuid;
use Helper;
use App\User;
use App\Dentist;
use App\DentMeta;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DentistController extends Controller
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
                $per_page = 25;
            }
            if (Auth::user()->hasRole('admin')) {
                $results = Dentist::join('dent_metas as dentmeta', 'dentmeta.dent_id', '=', 'dentists.id');
                // $results = $results->leftJoin('dates as dates', 'dates.patient_id', '=', 'patients.id');
                if ($request->input('orderby')) {
                    $results = $results->orderBy($request->input('orderby')['name'], $request->input('orderby')['sort']);
                } else {
                    $results = $results->orderBy('created_at', 'desc');
                }
                if ($request->input('filterby') && $request->input('filterby')['value'] != 'reset') {
                    $results = $results->where($request->input('filterby')['key'], $request->input('filterby')['value']);
                }
                if ($request->input('searchfor')) {
                    $results->where('dentists.name', 'like', '%' . $request->input('searchfor')['value'] . '%');
                }
                $results = $results->select('dentists.*');
                $results = $results->with(['dentmeta']);
                $results = $results->paginate($per_page);

                $all = Dentist::with(['dentmeta'])->count();
                // $confirmed = Patient::with(['dentmeta', 'lab', 'nextDate'])->where('confirmed', 1)->count();
                $response = [
                    'pagination' => [
                        'total'        => $results->total(),
                        'per_page'     => $results->perPage(),
                        'current_page' => $results->currentPage(),
                        'last_page'    => $results->lastPage(),
                        'from'         => $results->firstItem(),
                        'to'           => $results->lastItem(),
                    ],
                    'data'       => $results,
                    'stats'      => [
                        'all' => $all,
                        // 'confirmed' => $confirmed,
                    ],
                    'orderby'    => [
                        'name' => $request->input('orderby')['name'],
                        'sort' => $request->input('orderby')['sort'],
                    ],
                ];

                return $response;
            }
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function show($id)
    {
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('user')) {
            return Dentist::with(['dentmeta', 'labs', 'user'])->where('id', '=', $id)->first();
        } else {
            return Dentist::with(['dentmeta', 'labs', 'user'])->where('id', '=', Auth::user()->id)->first();
        }
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
        } else {
            $user = new User();

            $user->email = $request['user']['email'];
            $user->name = $request['user']['name'];
            $user->password = bcrypt($request['user']['password']);

            $user->save();

            $user->roles()->attach('4');

            $lab = new Dentist();

            $lab->name = $request['lab']['name'];
            $lab->status = 'inaktiv';
            $lab->user_id = $user->id;
            $lab->slug = str_slug($request['lab']['name'], '-');
            $lab->google_city = '';
            $lab->directtoken = UUid::generate('4');

            $geo = getLocation($request['labmeta']['zip']);
            $lab->lat = $geo['latitude'];
            $lab->lon = $geo['longitude'];

            $lab->save();

            $labmeta = new DentMeta();

            $labmeta->hello = $request['labmeta']['hello'];
            $labmeta->contact_person = $request['labmeta']['contact_person'];
            $labmeta->text = $request['labmeta']['text'];
            $labmeta->contact_email = $request['labmeta']['contact_email'];
            $labmeta->tel = $request['labmeta']['tel'];
            $labmeta->count = 0;
            $labmeta->street = $request['labmeta']['street'];
            $labmeta->city = $request['labmeta']['city'];
            $labmeta->zip = $request['labmeta']['zip'];
            #$labmeta->url = $request['labmeta']['url'];

            $lab->dentmeta()->save($labmeta);

            return "new user with lab saved";
        }
    }

    public function update(Request $request, $id)
    {
        $lab = Dentist::find($id);
        if ($lab->user_id == Auth::user()->id || Auth::user()->hasRole('admin')) {
            $meta = $lab->dentmeta;
            $meta->hello = $request['dentmeta']['hello'];
            $meta->contact_person = $request['dentmeta']['contact_person'];
            $meta->special1 = $request['dentmeta']['special1'];
            $meta->special2 = $request['dentmeta']['special2'];
            $meta->special3 = $request['dentmeta']['special3'];
            $meta->special4 = $request['dentmeta']['special4'];
            $meta->special5 = $request['dentmeta']['special5'];
            $meta->text = $request['dentmeta']['text'];
            $meta->contact_email = $request['dentmeta']['contact_email'];
            $meta->tel = $request['dentmeta']['tel'];
            $meta->street = $request['dentmeta']['street'];
            $meta->city = $request['dentmeta']['city'];
            $meta->zip = $request['dentmeta']['zip'];

            $meta->save();

            return "lab saved";
        }
    }

    public function refer($dent, $lab)
    {
        if (Auth::user()->hasRole('admin')) {
            $dent = Dentist::find($dent);
            $dent->labs()->attach($lab);

            return "success - dentist referred :)";
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

}
