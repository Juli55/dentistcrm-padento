<?php

use App\Email;
use Illuminate\Database\Seeder;

class BrandNewEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Email::create([
            'name'              => 'Zahnarzt-Labormail',
            'short_description' => '[Zahnarzt-Labor] Neuer Kontakt für Labor 2',
            'footer'            => 'Mit freundlichen Grüßen – Ihr Padento-Bot',
            'body'              => 'Sehr geehrter [name], ein/e Interessent/in hat sich über Padento gemeldet. Die Daten finden Sie wie gewohnt in Ihrem Login Bereich. Um dorthin zu gelangen klicken Sie <a href="http://padento.de/dashboard">hier</a>',
            'description'       => 'Das ist die Labormail die das Labor bekommt wenn ein Phasentermin für einen Zahnarzt erstellt wird',
            'subject'           => 'Ein neuer Interessent über Padento steht in Ihrem Login Bereich',
            'sort'              => 304,
        ]);
    }
}
