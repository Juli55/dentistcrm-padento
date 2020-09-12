<?php

namespace App\Http\Controllers\API;

use App\Country;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountriesController extends Controller
{
    public function index()
    {
        return Country::latest()->orderBy('created_at', 'desc')->get();
    }

    public function show($id)
    {
        return Country::findOrFail($id);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'country_name' => 'required',
            'country_code' => 'required',
        ]);

        return Country::create($request->only('country_name', 'country_code', 'zip_code'));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'country_name' => 'required',
            'country_code' => 'required',
        ]);

        $country = Country::findOrFail($id);

        $country->update($request->only('country_name', 'country_code'));

        return $country;
    }

    public function destroy($id)
    {
        return Country::destroy($id);
    }

    public function search(Request $request)
    {
        // we will be back to this soon!
    }
}
