<?php

use App\Country;
use Illuminate\Database\Seeder;

class ZipCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('zip_codes')->truncate();

        $de = Country::where('country_code', 'DE')->first();

        foreach ($this->getDEzipcodes() as $zip) {
            $de->zip_codes()->create([
                'zip_code' => str_replace("\n", "", $zip),
            ]);
        }

        $at = Country::where('country_code', 'AT')->first();

        foreach ($this->getATzipcodes() as $zip) {
            $at->zip_codes()->create([
                'zip_code' => str_replace("\n", "", $zip),
            ]);
        }
    }

    public function getDEzipcodes()
    {
        $de = app_path() . "/../plz.txt";

        return file($de);
    }

    public function getATzipcodes()
    {
        $at = app_path() . "/../plz.at.txt";

        return file($at);
    }

}
