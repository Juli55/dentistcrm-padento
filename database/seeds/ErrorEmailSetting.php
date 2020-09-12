<?php

use App\Settings;
use Illuminate\Database\Seeder;

class ErrorEmailSetting extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::firstOrCreate([
            'name' => 'Error Email Address',
            'value' => "",
        ]);
    }
}
