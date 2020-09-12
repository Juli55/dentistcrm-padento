<?php

use Illuminate\Database\Seeder;

class LabTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        #factory(App\Lab::class, 10)->create()->each(function($l) {
        #    $l->labmeta()->save(factory(App\LabMeta::class)->make());
        #});
        /*
        for($i=1;$i<11;$i++) {
            DB::table('labs')->insert([
                'user_id' => rand(1,10),
                'name' => 'Random Lab' . $i,
                'status' => 'aktiv',
                'google_city' => 'Berlin',
                'lat' => '12.12',
                'lon' => '52.3',
                'directtoken' => str_random(32)
        ]);
        */
    }
}
