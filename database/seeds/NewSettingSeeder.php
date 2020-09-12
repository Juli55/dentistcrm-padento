<?php

use App\Settings;
use Illuminate\Database\Seeder;

class NewSettingSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        if(!Settings::where('name', 'Textbereich 1')->exists()) {
            Settings::firstOrCreate([
                'name' => 'Textbereich 1',
                'value' => "<h3>Fragen, die immer wieder auftauchen:</h3>
						<ul>
							<li>Zeigt der Zahnarzt mir überhaupt <strong>alle Möglichkeiten</strong> und vor allem die für mich besten?</li>
							<li>Erhalte ich einen <strong>fairen Preis</strong>?</li>
							<li>Wird mein neuer Zahnersatz auch<strong> in einem deutschen Meisterlabor</strong> hergestellt?</li>
							<li>Wo bekomme ich eigentlich eine <strong>zweite, unabhängige Meinung?</strong></li>
						</ul>

						<h3>Mit Selbstbewusstsein Lachen, Sprechen und Essen:</h3>

						<ol>
							<li>Wir sind der Überzeugung, dass Zahnersatz <strong>nur in Begleitung des Zahntechnikers</strong> richtig gut werden kann.</li>
							<li>Unsere Partner sind ausgewählte Dentallabore und Zahnärzte aus</strong> <strong>ganz</strong> Deutschland.</strong></li>
							<li>Wir sind <strong>kein</strong> Billiganbieter und dennoch gibt es <strong>immer eine Lösung</strong>.</li>
							<li>Mit der Unterstützung des Zahntechnikermeisters bekommen Sie Infos vom <strong>Hersteller.</strong> Er ist der <strong>Profi für Zahnersatz.</strong></li>
							<li>Obwohl die <strong>Beratung sehr wertvoll</strong> für den Patienten ist, bieten wir diese <strong>kostenlos</strong> an.<br>
							</li>
						</ol>",
            ]);
        }

        if(!Settings::where('name', 'Textbereich 2')->exists()) {
            Settings::firstOrCreate([
                'name'  => 'Textbereich 2',
                'value' => "<ol>
							<li>Füllen Sie das <strong>Formular</strong> aus und tragen Sie Ihre Daten dort ein.</li>
							<li>Sie werden von uns telefonisch kontaktiert, um einen <strong>Termin</strong> zu machen.</li>
							<li>Sie gehen <strong>zusammen mit dem Zahntechniker zu einem ausgewählten Zahnarzt</strong>, von dem sich auch der Zahntechniker behandeln lassen würde.</li>
							<li>Ein ausgewählter Zahnarzt ist nicht teurer als andere Zahnärzte. <strong>Er ist einfach nur sehr gut</strong>.</li>
							<li>Sie bekommen zusätzlich eine <strong>zweite Meinung vom Zahnarzt</strong>, die bei den vielen Möglichkeiten heutzutage absolut wichtig ist.</li>
							<li>Der Zahntechniker lernt Sie als <strong>Mensch</strong> persönlich kennen und sieht nicht nur das Gips-Modell.</li>
							<li>Dadurch werden Ihre neuen Zähne <strong>viel besser zu Ihnen passen</strong>.</li>
							<li>Bei <strong>Angstpatienten</strong> werden spezielle Zahnärzte ausgesucht, die sich damit auskennen.</li>
						</ol>",
            ]);
        }

        if(!Settings::where('name', 'No Labs Found Title')->exists()) {
            Settings::firstOrCreate([
                'name' => 'No Labs Found Title',
                'value' => "Wir konnten kein Labor in Ihrer Nähe finden",
            ]);
        }

        if(!Settings::where('name', 'No Labs Found Body')->exists()) {
            Settings::firstOrCreate([
                'name' => 'No Labs Found Body',
                'value' => "<p class=\"centered\">Leider konnten wir kein Padento-Labor in Ihrer Nähe finden. Eventuell werden wir uns bei Ihnen melden und ein Alternativlabor vorschlagen.</p>",
            ]);
        }

        if(!Settings::where('name', 'Dev Email Address')->exists()) {
            Settings::firstOrCreate([
                'name' => 'Dev Email Address',
                'value' => "pascal@pinetco.com",
            ]);
        }

				if(!Settings::where('name', 'introduction vimeo welcome video')->exists()) {
            Settings::firstOrCreate([
                'name' => 'introduction vimeo welcome video',
                'value' => "https://player.vimeo.com/video/302066309?color=00aff5&title=0&byline=0&portrait=0",
            ]);
        }
	}
}
