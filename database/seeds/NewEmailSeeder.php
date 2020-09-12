<?php

use App\Email;
use Illuminate\Database\Seeder;

class NewEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Email::where('name', 'patientHasNoInterestMail')->delete();

        Email::create([
            'name'              => 'patientHasNoInterestMail',
            'short_description' => 'Patient has no interest',
            'footer'            => '',
            'body'              => '',
            'description'       => '',
            'subject'           => '',
            'sort'              => null,
        ]);

        Email::where('name', 'Labormail2')->delete();

        Email::create([
            'name'              => 'Labormail2',
            'short_description' => '[Labor] Neuer Kontakt für Labor 2',
            'footer'            => 'Mit freundlichen Grüßen – Ihr Padento-Bot',
            'body'              => 'Sehr geehrter [name], ein/e Interessent/in hat sich über Padento gemeldet. Die Daten finden Sie wie gewohnt in Ihrem Login Bereich. Um dorthin zu gelangen klicken Sie <a href="http://padento.de/dashboard">hier</a>',
            'description'       => 'Das ist die Labormail die das Labor bekommt, wird ein Kontakt an dass Labor verwiesen.',
            'subject'           => 'Ein neuer Interessent über Padento steht in Ihrem Login Bereich',
            'sort'              => 301,
        ]);
    }
}
