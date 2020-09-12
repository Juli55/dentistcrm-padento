<?php

use App\Email;
use Illuminate\Database\Seeder;

class DeleteAccountMailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Email::create([
            'name'              => 'DeleteAccount',
            'short_description' => 'E-Mail zur Bestätigung der Löschung von Daten des Kontaktes',
            'footer'            => '',
            'body'              => '',
            'description'       => '',
            'subject'           => '',
            'sort'              => 800,
        ]);

        Email::create([
            'name'              => 'AccountDeleted',
            'short_description' => 'Your padento account has been deleted',
            'footer'            => '',
            'body'              => '',
            'description'       => '',
            'subject'           => '',
            'sort'              => 801,
        ]);
    }
}
