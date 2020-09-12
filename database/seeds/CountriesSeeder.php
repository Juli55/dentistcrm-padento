<?php

use App\Country;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Country::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Country::create([
            'country_name' => 'Deutschland',
            'country_code' => 'DE',
        ]);

        Country::create([
            'country_name' => 'Österreich',
            'country_code' => 'AT',
        ]);
    }
}
