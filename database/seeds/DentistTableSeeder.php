<?php

use Illuminate\Database\Seeder;

class DentistTableSeeder extends Seeder
{
    public function run()
    {
        #factory(App\Dentist::class, 10)->create()->each(function($l) {
            #$l->dentmeta()->save(factory(App\DentMeta::class)->make());
        #});
    }
}
