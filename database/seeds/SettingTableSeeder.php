<?php

use App\Email;
use App\Settings;
use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{
	public function run()
	{
		Settings::truncate();
		Email::truncate();

		Settings::create([
			'name'        => 'Patientenradius',
			'value'       => '40',
			'description' => 'Der maximale Patienten-Radius',
		]);

		Email::create([
			'name'        => 'Testmail',
			'footer'      => 'Das ist der Footer',
			'body'        => 'Das ist nur eine Testmail.',
			'description' => 'Einfach eine Testmail.',
		]);

		Email::create([
			'name'        => 'Kontaktmail1',
			'footer'      => 'Das ist der Footer',
			'body'        => "Sehr geehrte/r Frau/Herr [name]
=
Vielen Dank für Ihr Interesse an einem [grün]kostenlosen Beratungstermin[/grün].
Bitte klicken Sie auf den folgenden Button um Ihre Anfrage zu bestätigen: [bestätigungslink]",
			'description' => 'Das ist die erste "Bestätigungsmail".',
		]);

		Email::create([
			'name'        => 'Kontaktmail2',
			'footer'      => 'Das ist der Footer',
			'body'        => 'Das ist die zweite Kontaktmail.',
			'description' => 'Das ist die zweite Kontakt-Email.',
		]);

		Email::create([
			'name'        => 'Labormail1',
			'footer'      => 'Mit freundlichen Grüßen – Ihr Padento-Bot',
			'body'        => 'Sehr geehrter [name], ein/e Interessent/in hat sich über Padento gemeldet. Die Daten finden Sie wie gewohnt in Ihrem Login Bereich. Um dorthin zu gelangen klicken Sie <a href="http://padento.de/dashboard">hier</a>',
			'description' => 'Das ist die Labormail die das Labor bekommt, wird ein Kontakt an dass Labor verwiesen.',
		]);
	}
}
