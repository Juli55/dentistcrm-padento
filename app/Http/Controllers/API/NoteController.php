<?php

namespace App\Http\Controllers\API;

use Auth;
use App\PatientNote;
use App\DentistNote;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function destroy($id)
    {
        $note = PatientNote::find($id);
        if (Auth::user()->hasRole('admin')) {
            $note->delete();
        } else {
            return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
        }

        return redirect()->back();
    }

    public function destroydentistnote($id)
    {
        $note = DentistNote::find($id);
        if (Auth::user()->hasRole('admin')) {
            $note->delete();
        } else {
            return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
        }

        return redirect()->back();
    }

}
