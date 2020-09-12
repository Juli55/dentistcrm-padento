<?php

use App\Email;
use Illuminate\Database\Seeder;

class DsgvoAutoDeleteMailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Email::create([
            'name'              => 'DSGVOAutoDeleteReminder',
            'short_description' => 'DSGVO auto delete reminder',
            'footer'            => '',
            'body'              => '',
            'description'       => '',
            'subject'           => '',
            'sort'              => 802,
        ]);

        Email::create([
            'name'              => 'DSGVOAutoDeletePatientMailForLab',
            'short_description' => 'DSGVO auto delete patient mail for lab',
            'footer'            => '',
            'body'              => '',
            'description'       => '',
            'subject'           => '',
            'sort'              => 803,
        ]);
    }
}
