<?php

use App\Settings;
use Illuminate\Database\Seeder;

class StartPageSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::create([
            'name'        => 'Hauptüberschrift',
            'value'       => 'Zahntechnikermeister helfen Ihnen beim Thema Zahnersatz',
            'description' => '',
        ]);

        Settings::create([
            'name'        => 'Text unterhalb Hauptüberschrift',
            'value'       => "Wir haben <strong>über 14.800 Menschen kennengelernt, die unzufrieden mit dem Ablauf bei Ihrem Zahnarzt und unzureichend über die Möglichkeiten aufgeklärt worden sind.</strong> Andere wollen einfach eine zweite Meinung und mehr Sicherheit bei ihrer Entscheidung. Viele lassen sich deshalb <strong>aus zwei Perspektiven beraten: Vom Zahnarzt und vom Zahntechniker</strong>.",
            'description' => '',
        ]);

        Settings::create([
            'name'        => 'Wistia Video Code',
            'value'       => 'k41j7v41lk',
            'description' => '',
        ]);
    }
}
