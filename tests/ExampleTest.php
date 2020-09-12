<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testStartpage()
    {

        echo "\nTesting some pages:";
        echo "\n - /";
        $this->visit('/')
             ->see('Zahntechnikermeister informieren Sie');
        echo "\t\t\t => \e[42m\e[97mok\e[49m\e[37m";
        echo "\n - /labor/rainer-ehrich";
        $this->visit('/labor/rainer-ehrich')
            ->see('Ihr Ansprechpartner')
            ->see('Rainer Ehrich');
        echo "\t => \e[42m\e[97mok\e[49m\e[37m";
        echo "\n\nTesting to reach /admin/dashboard without being logged in\n";
        $this->visit('/app')
            ->see('Anmelden');
        $this->visit('/api/labs')
            ->see('Anmelden');
        $this->visit('/api/lab/1')
            ->see('Anmelden');
        echo "\t\t\t => \e[42m\e[97mforbidden\e[49m\e[37m";
    }

    public function testLogin ()
    {
        echo "\n\nTesting Login/out:";
        echo "\n - Login...";
        $this->visit('/login')
            ->see('Anmelden');
        $this->type('pascal@pinetco.com', 'email');
        $this->type('testtest', 'password')
            ->press('Anmelden');
        echo "\t\t => \e[42m\e[97mok\e[49m\e[37m";
        echo "\n - Dashboard";
        $this->see('Padento-Karte');
        $this->visit('/app')
            ->see('Padento-Karte');
        $this->visit('/app')
            ->see('Marc');
        echo "\t\t => \e[42m\e[97mok\e[49m\e[37m";
        echo "\n - Logout";
        $this->visit('/logout')
            ->see('Zahntechnikermeister informieren Sie');
        echo "\t\t => \e[42m\e[97mok\e[49m\e[37m";
    }

    public function testAPI ()
    {
        echo "\n\nTesting some simple API calls...";
        ###-- as guest --##
        echo "\n - as Guest";
        echo "\n   - /api/labs";
        $this->visit('/api/labs')
            ->see('Login');
        echo "\t\t => \e[42m\e[97mforbidden\e[49m\e[37m";
        echo "\n   - /api/allcontacts";
        $this->visit('/api/allcontacts')
            ->see('Login');
        echo "\t => \e[42m\e[97mforbidden\e[49m\e[37m";
        echo "\n   - /api/logs";
        $this->visit('/api/logs')
            ->see('Login');
        echo "\t\t => \e[42m\e[97mforbidden\e[49m\e[37m";
        echo "\n   - /api/users/all";
        $this->visit('/api/users/all')
            ->see('Login');
        echo "\t => \e[42m\e[97mforbidden\e[49m\e[37m";

        ###-- as admin --###
        echo "\n - as Admin";
        echo "\n   - /api/labs";
        $this->actingAs(\App\User::find(1))
            ->visit('/api/labs')
            ->seeJson([
                'id' => 1,
                'user_id' => "2"
            ]);
        echo "\t\t => \e[42m\e[97mok\e[49m\e[37m";
        echo "\n   - /api/allcontacts";
        $this->actingAs(\App\User::find(1))
            ->visit('/api/allcontacts')
            ->seeJson([
                'id' => 1,
                'archived' => "0"
            ]);
        echo "\t => \e[42m\e[97mok\e[49m\e[37m";
        echo "\n   - /api/logs";
        $this->actingAs(\App\User::find(1))
            ->visit('/api/logs')
            ->seeJson([
                'id' => 1,
                'user_id' => "1"
            ]);
        echo "\t\t => \e[42m\e[97mok\e[49m\e[37m";
        echo "\n   - /api/users/all";
        $this->actingAs(\App\User::find(1))
            ->visit('/api/users/all')
            ->seeJson([
                'id' => 1,
                'name' => "Marc"
            ]);
        echo "\t => \e[42m\e[97mok\e[49m\e[37m";

        ###-- as lab --###
        echo "\n - as Lab";
        echo "\n   - /api/labs";
        $this->actingAs(\App\User::find(3))
            ->visit('/api/labs')
            ->seeJson([
                'user_id' => "3"
            ]);
        echo "\t\t => \e[42m\e[97mok\e[49m\e[37m";
        echo "\n   - /api/allcontacts";
        $this->actingAs(\App\User::find(3))
            ->visit('/api/allcontacts')
            ->seeJson([
            ]);
        echo "\t => \e[42m\e[97mok\e[49m\e[37m";
        echo "\n   - /api/logs";
        $this->actingAs(\App\User::find(3))
            ->visit('/api/logs')
            ->seeJson([
                'status' => "403",
            ]);
        echo "\t\t => \e[42m\e[97mforbidden\e[49m\e[37m";
        echo "\n   - /api/users/all";
        $this->actingAs(\App\User::find(3))
            ->visit('/api/users/all')
            ->seeJson([
                'status' => "403",
            ]);
        echo "\t => \e[42m\e[97mforbidden\e[49m\e[37m";
    }

    public function testPatientForm ()
    {
        $this->expectsEvents(App\Events\PatientFormFilled::class);
        echo "\n\nDeleting Test-Patients";
        $old = \App\Patient::with(['patientmeta' => function($q) {
            $q->where('name', '=', 'Herr Unittest');
        }])->get();
        foreach($old as $o) {
            if ($o->patientmeta) {
                if ($o->patientmeta->name == 'Herr Unittest') {
                    $o->delete();
                }
            }
        };
        echo "\t => \e[42m\e[97mok\e[49m\e[37m";

        echo "\n\nTesting Patient-Form:";
        echo "\n - submitting form";
        $this->visit('/')
            ->see('"Bitte geben Sie Ihre Postleitzahl hier ein.')
            ->type('42281', 'plz')
            ->type('Herr Unittest', 'name')
            ->type('pinetco@gmx.de', 'mail')
            ->type('1234567890', 'tel')
            ->press('submitform')
            ->see('Schauen Sie jetzt in Ihr E-Mail Postfach!');
        echo "\t => \e[42m\e[97mok\e[49m\e[37m";
        $old = \App\Patient::with(['patientmeta' => function($q) {
            $q->where('name', '=', 'Herr Unittest');
        }])->get();
        echo "\n\nSearching for new entry in DB:";
        $count = \App\PatientMeta::where('name', '=', 'Herr Unittest')->get()->count();
        echo "\n - found $count entry";
        if ($count == 1) {
            echo "\t => \e[42m\e[97mok\e[49m\e[37m";
        } else {
            echo "\t => \e[41m\e[97merror\e[49m\e[37m";
        }
    }

    public function testPatientFormErrors ()
    {

        $this->expectsEvents(App\Events\PlzIsMissing::class);
        $this->expectsEvents(App\Events\NoLabWasFound::class);
        echo "\n\nDeleting Test-Patients";
        $old = \App\Patient::with(['patientmeta' => function($q) {
            $q->where('name', '=', 'Herr Unittest');
        }])->get();
        foreach($old as $o) {
            if ($o->patientmeta) {
                if ($o->patientmeta->name == 'Herr Unittest') {
                    $o->delete();
                }
            }
        };
        echo "\t => \e[42m\e[97mok\e[49m\e[37m";

        echo "\n\nTesting Patient-Form-Errors:";
        echo "\n - nonexistent PLZ";
        $this->visit('/')
            ->see('"Bitte geben Sie Ihre Postleitzahl hier ein.')
            ->type('42222', 'plz')
            ->type('Herr Unittest', 'name')
            ->type('herrunittest@trash-mail.com', 'mail')
            ->type('1234567890', 'tel')
            ->press('submitform')
            ->see('Es wurde leider kein Labor');
        echo "\t => \e[42m\e[97mok\e[49m\e[37m";
        $old = \App\Patient::with(['patientmeta' => function($q) {
            $q->where('name', '=', 'Herr Unittest');
        }])->get();
        echo "\n\nTesting Patient-Form-Errors:";
        echo "\n No Lab Found";
        $this->visit('/')
            ->see('"Bitte geben Sie Ihre Postleitzahl hier ein.')
            ->type('36286', 'plz')
            ->type('Herr Unittest', 'name')
            ->type('herrunittest@trash-mail.com', 'mail')
            ->type('1234567890', 'tel')
            ->press('submitform')
            ->see('Es wurde leider kein Labor');
        echo "\t\t => \e[42m\e[97mok\e[49m\e[37m";
        $old = \App\Patient::with(['patientmeta' => function($q) {
            $q->where('name', '=', 'Herr Unittest');
        }])->get();
        echo "\n\nSearching for new entry in DB:";
        $count = \App\PatientMeta::where('name', '=', 'Herr Unittest')->get()->count();
        echo "\n - found $count entries";
        if ($count == 0) {
            echo "\t => \e[42m\e[97mok\e[49m\e[37m";
        } else {
            echo "\t => \e[41m\e[97merror\e[49m\e[37m";
        }
    }
    
}
