<?php

namespace App\Http\Controllers\API;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Services\Stats\Contact;
use App\Services\Stats\DentistContact;
use App\Http\Controllers\Controller;

class StatsController extends Controller
{
    public function contacts()
    {
        $stats = app(Contact::class)->handle();

        return response()->json($stats);
    }

    public function dentistcontacts()
    {
        $stats = app(DentistContact::class)->handle();

        return response()->json($stats);
    }
}
