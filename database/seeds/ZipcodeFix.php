<?php

use Illuminate\Database\Seeder;

class ZipcodeFix extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $zipcodes = \App\ZipCode::all();

        foreach ($zipcodes as $zipcode) {
            $zipcode->zip_code = str_replace("\n", "", $zipcode->zip_code);

            $zipcode->save();
        }
    }
}
