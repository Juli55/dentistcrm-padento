<?php

namespace App\Http\Controllers\API;

use App\Lab;
use App\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LabUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getLabUsersCount()
    {
        $lab = Auth()->user()->lab;

        if (count($lab) == 0)
            $lab = Auth()->user()->labs;

        return $lab->first()->users()->where('status', 'Aktiv')->count();
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $lab = $user->lab->first();

        if (!$lab) {
            $lab = $user->labs->first();
        }


        if ($request->input('pagination')['per_page']) {
            $per_page = $request->input('pagination')['per_page'];
        } else {
            $per_page = 100;
        }
        $lab = Lab::where('id', $lab->id)->first();

        $results = $lab->users()->withCount('activity')->paginate($per_page);

        $response = [
            'pagination' => [
                'total' => $results->total(),
                'per_page' => $results->perPage(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'from' => $results->firstItem(),
                'to' => $results->lastItem(),
            ],
            'data' => $results,
            'orderby' => [
                'name' => $request->input('orderby')['name'],
                'sort' => $request->input('orderby')['sort'],
            ],
        ];

        return $response;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        $user = $request->user();

        $lab = $user->lab->first();

        if (!$lab) {
            $lab = $user->labs->first();
        }

        $newUser = User::create(
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]
        );

        $newUser->roles()->attach($user->roles);

        $lab->users()->attach($newUser->id);

        return "lab user created";
    }

    public function destroy()
    {
        $user = User::find(request('id'));

        if(auth()->id() == $user->labs()->first()->user_id && !$user->activity()->count()) {
            $user->labs()->detach();

            $user->delete();

            return response()->json('success');
        }
    }

    public function toggleActive()
    {
        $user = User::find(request('user_id'));

        if($user->status == 'Aktiv') {
            $user->status = 'Deaktiviert';
            $user->allowlogin = 'disallow';
        } else {
            $user->status = 'Aktiv';
            $user->allowlogin = 'allow';
        }

        $user->save();

        return $user;
    }
}
