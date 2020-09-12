<?php

use Illuminate\Database\Seeder;

class LabMetaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        for ($i=1;$i<12;$i++) {
            DB::table('lab_metas')->insert([
                'lab_id' => $i,
                'hello' => str_random(64),
                'contact_person' => 'Kontaktperson ' . $i,
                'special1' => 'Special ' . $i,
                'special2' => 'Special ' . $i,
                'special3' => 'Special ' . $i,
                'special4' => 'Special ' . $i,
                'special5' => 'Special ' . $i,
                'text' => 'Der Labortext...',
                'contact_email' => 'contact@example.com',
                'tel' => '123456789',
                'count' => rand(1,500),
                'street' => str_random(8) . ' Strasse ' . rand(1,100),
                'city' => str_random(8),
                'zip' => rand(10000,99999)
            ]);
        }
        */
    }
}
