<?php

if (config('app.debug')) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

use App\Date;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/** TEST CSV */
Route::get('test', 'PublicPageController@test');
Route::get('addWelcomeVideoSetting', 'PublicPageController@addWelcomeVideoSetting');
Route::get('addFormVideoSetting', 'PublicPageController@addFormVideoSetting');

Route::get('migrate/images', 'MigrationController@images');

Route::get('stats/test', function () {
    return view('home');
});
Route::get('api/stats/{func}', function ($function) {
    return (new \App\Http\Controllers\API\StatsController())->$function();
});

/** Logs */
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

//Redirects
Route::get('dashboard', function () {
    return redirect('app/dashboard');
});
Route::get('neues-Labor', function () {
    return redirect('neues-labor');
});
Route::get('was-ist-padento', function () {
    return redirect('/');
});

Route::get('e-book-2015-04', function () {
    return redirect('/?utm_source=ebook');
});

Route::group(['middleware' => ['auth']], function () {
});

Route::get('account/remove', 'AccountController@index')->name('account.remove');
Route::get('account/remove/confirm', 'AccountController@confirm')->name('account.remove.confirm');
Route::post('account/remove/send-confirmation', 'AccountController@store')->name('account.remove.send-confirmation');

Route::get('dsgvo/accept', 'DsgvoController@index')->name('dsgvo.accept');

Route::get('attachments/upload/{token}', 'AttachmentController@index');
Route::post('attachments/upload/{token}', 'AttachmentController@store');

// New Dentist CRM Upload
Route::get('attachments/uploaddentist/{token}', 'AttachmentController@indexdentist');
Route::post('attachments/uploaddentist/{token}', 'AttachmentController@storedentist');

Route::get('attachments/{attachment}', 'AttachmentController@show')->middleware('auth');

Route::get('dankedentist', 'PublicPageController@newDentistContact');
Route::post('dankedentist', 'PublicPageController@newDentistContact');

Route::get('danke', 'PublicPageController@newRequest');
Route::get('formpage', 'PublicPageController@formpage');
Route::post('danke', 'PublicPageController@newRequest');
Route::post('danke/blog', 'PublicPageController@newRequest');
Route::get('zahnarzt/{name}', 'LabController@showDentist');
Route::get('videos', 'PublicPageController@videos');
Route::get('impressum', 'PublicPageController@impressum');
Route::get('datenschutzerklaerung', 'PublicPageController@datenschutzerklaerung');
Route::post('wann', 'PatientController@bestTime');

Route::get('flyer', function () {
    return redirect('/');
});

Route::get('entschuldigung', function () {
    return view('pages.sorry-no-lab');
});
Route::get('als-labor-mitmachen', function () {
    return view('pages.request-membership');
});

Route::post('vielen-dank-fuer-ihre-anfrage', ['as' => 'request', 'uses' => 'LabController@labRequest']);

Route::get('lb/{id}', ['as' => 'direct.link', 'uses' => 'PublicPageController@direct']);
Route::get('l/{slug}', ['as' => 'direct.link', 'uses' => 'PublicPageController@direct']);
Route::get('mailtoken/{token}', 'PatientController@mailtoken');
Route::auth();

Route::get('app/{vue_capture?}', function () {
    return View::make('backend.admin.dashboard');
})->where('vue_capture', '[\/\w\.-]*')->middleware('auth');

Route::group(['prefix' => 'downloads'], function () {
    Route::get('ueber-padento', function () {
        return response()->download('../downloads/Ueber-Padento.pdf');
    });
    Route::get('pressetext', function () {
        return response()->download('../downloads/Pressetext.pdf');
    });
    Route::get('aufkleber', function () {
        return response()->download('../downloads/Padento-Aufkleber.zip');
    });
    Route::get('aufkleber-anleitung', function () {
        return response()->download('../downloads/Anleitung_Autobeklebung.pdf');
    });
    Route::get('guide/padento-einfuehrung', function () {
        return response()->download('../downloads/Padentoguide_Teil1_Technik_12.16.pdf');
    });
    Route::get('guide/patienten-gewinnen', function () {
        return response()->download('../downloads/Padentoguide_Teil2_Neue_Patienten_12.16.pdf');
    });
    Route::get('telefon-fragebogen', function () {
        return response()->download('../downloads/Telefonkontakt-Fragebogen.pdf');
    });
    Route::get('padentogespraechsleitfaden', function () {
        return response()->download('../downloads/Padentogespraech.pdf');
    });
    Route::get('padento_checkliste_gespraeche', function () {
        return response()->download('../downloads/Checkliste_Patientengespraech_12_16.pdf');
    });
    Route::get('padento_termine_machen', function () {
        return response()->download('../downloads/Padento_Termine_machen.pdf');
    });
    Route::get('padento_wie_funktioniert_der_telefonservice', function () {
        return response()->download('../downloads/Wie_funktioniert_der_Telefonservice_fuer_Labore_12_16.pdf');
    });
    Route::get('mail_vorlagen', function () {
        return response()->download('../downloads/Mailvorlagen_12.16_neu.pdf');
    });

    Route::get('logos', function () {
        return response()->download('../downloads/Padento-Logo-Pack.zip');
    });
});

Route::group(['prefix' => 'api', 'namespace' => 'API'], function () {

    Route::post('stats/contacts', 'StatsController@contacts');
    Route::post('stats/dentists-contacts', 'StatsController@dentistcontacts');

    // Patient
    Route::get('patients/export', 'PatientController@export');
    Route::get('patients/export/download/{filename}', 'PatientController@downloadExport');

    // Lab
    Route::get('labs', 'LabController@index')->name('api.lab.index');
    Route::get('labs/{id}/images', 'LabController@images')->name('api.lab.images');
    Route::get('labs/{id}/check-crm-access', 'LabController@checkCrmAccess')->name('api.lab.check-crm-access');
    Route::post('labs/{id}/toggle-crm-access', 'LabController@toggleCrmAccess')->name('api.lab.toggle-crm-access');

    //Lab users
    Route::get('allLabUsers', 'LabUserController@index')->name('api.labUser.index');
    Route::post('allLabUsers', 'LabUserController@index')->name('api.labUser.index');
    Route::post('createLabUsers', 'LabUserController@store')->name('api.labUser.store');
    Route::get('getLabUsersCount', 'LabUserController@getLabUsersCount')->name('api.labUser.count');
    Route::post('lab-users/delete', 'LabUserController@destroy')->name('api.labUser.delete');
    Route::post('lab-users/toggle-active', 'LabUserController@toggleActive')->name('api.labUser.toggleActive');

    // Lab Dentist CRM
    Route::get('labs/{id}/check-dentist-crm-access', 'LabController@checkDentistCrmAccess')->name('api.lab.check-dentist-crm-access');
    Route::post('labs/{id}/toggle-dentist-crm-access', 'LabController@toggleDentistCrmAccess')->name('api.lab.toggle-dentist-crm-access');
    // End Lab Dentist CRM

    // Lab Multiple Users
    Route::get('labs/{id}/check-has-extra-users', 'LabController@checkHasExtraUsers')->name('api.lab.check-has-extra-users');
    Route::post('labs/{id}/toggle-has-extra-users', 'LabController@toggleHasExtraUsers')->name('api.lab.toggle-has-extra-users');
    // End Lab Multiple Users

    Route::get('lab/{id}', 'LabController@show')->name('api.single.lab');
    Route::post('lab/{id}', 'LabController@store')->name('api.single.lab');
    Route::get('lab/{id}/delete', 'LabController@destroy')->name('api.single.lab.delete');
    Route::post('newlab', 'LabController@newLab')->name('api.new.lab');
    Route::get('lab/{id}/stats', 'LabController@singleLabStats')->name('api.lab.stats');
    Route::get('allocatelab/{user}/{lab}', 'LabController@allocateLab')->name('api.allocate.lab');
//    Route::post('newlabmeta', 'LabController@newLabMeta')->name('api.new.lab');   // Controller method doesn't exists

    // File
    Route::post('fileUpload', 'FileController@store');
    Route::post('removeFile', 'FileController@destroy');
    Route::post('images/sort', 'ImageController@sort');
    Route::delete('images/{id}', 'ImageController@destroy');
    Route::get('images/sync', 'ImageController@sync');

    // User
    Route::get('users/all', 'UserController@index')->name('api.all.users');
    Route::get('user/{id}', 'UserController@view')->name('api.single.user');
    Route::post('user/{id}', 'UserController@update')->name('api.single.user');
    Route::delete('user/{id}/delete', 'UserController@delete')->name('api.single.user');
    Route::get('userpassword/{id}', 'UserController@view')->name('api.single.user');
    Route::post('userpassword/{id}', 'UserController@updateUserPassword')->name('api.single.user');
    Route::get('whoami', 'UserController@whoami');

    // Contact
    Route::get('allcontacts', 'ContactController@allContacts')->name('api.all.contacts');
    Route::get('contacts', 'ContactController@index')->name('api.all.contacts');
    Route::post('allcontacts', 'ContactController@allContacts')->name('api.all.contacts');
    Route::get('latestcontacts', 'ContactController@latestContacts')->name('api.all.contacts');
    Route::get('contact-stats', 'ContactController@contactStats')->name('api.contactstats');
    Route::post('contact/delete', 'ContactController@deleteContact');
    Route::post('contact/moveback', 'ContactController@movebackContact');
    Route::post('contact/sendReminder', 'ContactController@sendReminder');
    Route::get('contact/{id}', 'ContactController@singleContact')->name('api.single.contact');
    Route::get('contact/{id}/nextDate', 'ContactController@nextPatientDate')->name('api.contact.nextdate');
    Route::post('contact/{id}/save-date', 'ContactController@saveDate')->name('api.contact.save.date');
    Route::post('contact/{id}/update', 'ContactController@updateContact')->name('api.single.contact');
    Route::post('contact/used', 'ContactController@usedContacts');
    Route::post('contact/{contact}/refer/{lab}/{unobtainable}', 'ContactController@refer')->name('api.contact.refer');
    Route::post('contact/{id}/note', 'ContactController@saveNote')->name('api.save.note');

    // NEW DENTIST CONTACT CRM
    Route::get('dentist/{id}', 'DentistContactController@singleDentist')->name('api.single.dentist');
    Route::get('alldentists', 'DentistContactController@allDentists')->name('api.all.dentists');
    Route::post('alldentists', 'DentistContactController@allDentists')->name('api.all.dentists');
    Route::post('dentist/used', 'DentistContactController@usedDentists');
    Route::post('dentist/{id}/note', 'DentistContactController@saveNote')->name('api.save.note');
    Route::post('dentist/delete', 'DentistContactController@deleteDentist');
    Route::post('dentist/{id}/update', 'DentistContactController@updateContact')->name('api.single.dentist');
    Route::post('dentist/sendReminder', 'DentistContactController@sendReminder');
    Route::post('dentist/{id}/save-date', 'DentistContactController@saveDate')->name('api.dentist.save.date');

    // END DENTIST CRM

    // NEW DENTIST CONTACT CRM NOTES
    Route::get('note/{id}/delete', 'NoteController@destroy')->name('api.delete.note');
    Route::get('note/{id}/deletedentistnote', 'NoteController@destroydentistnote')->name('api.delete.note');
    // END DENTIST CRM

    /*
    // Dentist
    Route::get('alldentists', 'DentistController@index')->name('api.all.dentists');
    Route::post('dentist/{dentinst}/lab/{lab}', 'DentistController@refer');
    Route::get('dentist/{id}', 'DentistController@show')->name('api.single.dentist');
    Route::post('newdent', 'DentistController@store')->name('api.new.dent');
    Route::post('dentist/{id}', 'DentistController@update')->name('api.single.dentist');

    */

    // Setting
    Route::get('settings/email', 'SettingController@emailSettings')->name('api.email.settings');
    Route::post('settings/email/{id}', 'SettingController@saveEmail')->name('api.email.settings');
    Route::get('settings/all', 'SettingController@getSettings')->name('api.all.settings');
    Route::post('settings/all/{id}', 'SettingController@storeSettings')->name('api.all.settings');
    Route::get('settings/patient-props', 'SettingController@getProperties')->name('api.patient.properties');
    Route::get('properties/all', 'SettingController@getProperties')->name('api.patient.properties');
    Route::post('settings/single-property/{id}', 'SettingController@storeProperty')->name('api.save.single.property');
    Route::get('settings/single-property/{id}', 'SettingController@singleProperty')->name('api.single.property');
    Route::post('settings/property/new', 'SettingController@newProperty')->name('api.new.property');
    Route::post('settings/property/{id}', 'SettingController@storePropertyTemplate')->name('api.save.property');
    Route::get('settings/change-property-status/{id}', 'SettingController@changePropertyStatus')->name('api.change.property-status');

    // Lab Setting
    Route::get('settings/lab', 'LabSettingController@getSettings');
    Route::post('settings/lab/{id}', 'LabSettingController@storeSetting');
    Route::post('settings/lab/exclude-day/{id}', 'LabSettingController@excludeDay');
    Route::delete('settings/lab/{id}', 'LabSettingController@removeSetting');
    Route::get('timeframes/{id}', 'LabSettingController@getTimeFrames')->name('api.timeframes');

    // Role
    Route::get('roles/all', 'RoleController@allRoles')->name('api.all.roles');
    Route::get('user/{user}/role/{role}', 'RoleController@setUserRole')->name('api.set.userrole');

    // Timeframe
    Route::get('weekdays', 'TimeframeController@getWeekdays');
    Route::get('lab/{id}/timeframes', 'TimeframeController@getTimeFrames')->name('api.get.timeframes');
    Route::post('lab/{id}/timeframes/', 'TimeframeController@store')->name('api.save.timeframes');
    Route::post('timeframe/{id}/remove', 'TimeframeController@destroy')->name('api.remove.timeframes');

    Route::get('feeds', 'FeedController@index')->name('api.feeds');

    //Dentist  Date
    Route::get('mydates/dentist', 'DentistDateController@myDates')->name('api.dentist.dates');
    Route::post('mydates/dentist', 'DentistDateController@myDates')->name('api.dentist.dates');
    Route::get('dates/dentist/{id}', 'DentistDateController@getDates')->name('api.dentist.dates');
    Route::post('date/dentist/delete', 'DentistDateController@deleteDate')->name('api.dentist.dates');
    Route::get('latestdates/dentist/{id}', 'DentistDateController@getLatestDates')->name('api.dentist.latest.dates');
    Route::get('employeedate/get/dentist/{id}', 'DentistDateController@getEmployeeDate');
    Route::post('employeedate/dentist/save', 'DentistDateController@saveEmployeeDate');

    // Date
    Route::get('dates/{id}', 'DateController@getDates')->name('api.dates');
    Route::post('date/delete', 'DateController@deleteDate')->name('api.dates');
    Route::post('date/delete/dentist', 'DateController@deleteDentistDate')->name('api.dentist.dates');
    Route::post('mydates', 'DateController@myDates')->name('api.dates');
    Route::get('latestdates/{id}', 'DateController@getLatestDates')->name('api.latest.dates');
    Route::get('employeedate/get/{id}', 'DateController@getEmployeeDate');
    Route::post('employeedate/save', 'DateController@saveEmployeeDate');

    Route::get('timeline', 'TimelineController@index');
    Route::post('timeline/forContact', 'TimelineController@forContact');
    Route::post('note-timeline/forContact', 'TimelineController@notesForContact');

    Route::post('timeline/dentist/forContact', 'TimelineController@forDentistContact');
    Route::post('note-timeline/dentist/forContact', 'TimelineController@notesForDentistContact');

    //Links
    Route::get('links/getParentLinks', 'LinkController@getParentLinks');
    Route::get('links/prepareLinks', 'LinkController@prepareLinks');
    Route::post('links/sort', 'LinkController@sort');
    Route::resource('links', 'LinkController');

    // Test
    Route::post('send/test-mail/{id}', 'TestController@sendTestmail')->name('api.send.testmail');
});

Route::group(['prefix' => 'api'], function () {
    Route::get('logs', ['as' => 'api.logs', 'uses' => 'ApiController@showLogs']);

    Route::get('smsreminder', function () {
        // exec('php artisan schedule:run >> /dev/null 2>&1');
//        \Padento\Sms\SMS::testsms();
        \Padento\Sms\SMS::sendDateReminders();
        // return $date->patient->patientmeta->salutation;
        // return $dates;
    });

//    Route::get('attachmentReminder', function() {
//        if ($_SERVER['REMOTE_ADDR'] == '85.13.157.244' || config('app.debug')) {
//            (new \App\Console\Commands\CheckAttachments())->handle();
//        }
//    });

    // Route::get('distribution/refresh', 'PublicPageController@refreshLatLon');
    Route::get('distribution/circlesettings', 'PublicPageController@circleSettings');
    Route::get('distribution/lookup/{plz}/{country?}', 'PublicPageController@lookup');
    Route::get('distribution/{plz}/{country?}/{mail?}', 'PublicPageController@testplz');
});

Route::get('loginas/{id}', function ($id) {
//    Auth::loginUsingId($id);
});

Route::get('sendsmstest', function () {
//    \Padento\Sms\SMS::testsms();
//    \Padento\Sms\SMS::sendDateReminders();
});

##-- admin stuff --##
Route::get('admin/contact/{patient}/toggle-phase/{phase}', ['as' => 'admin.change.phase', 'uses' => 'PatientController@changePhase']);
//For Dentist
Route::get('admin/dentist/{patient}/toggle-phase/{phase}', ['as' => 'admin.change.phase', 'uses' => 'PatientController@changePhaseForDentist']);
//End For Dentist
Route::get('admin/change-user-status/{id}', ['as' => 'api.change.user-status', 'uses' => 'ApiController@changeUserStatus']);
##-- api stuff --##

Route::get('dashboard/karte', function () {
    if (Auth::check()) {
        $glabs = \App\Lab::where('status', '=', 'aktiv')->get();

        return view('backend.karte2', ['geolong' => '9.35729', 'geolat' => '51.551378', 'glabs' => $glabs]);
    } else {
        return abort(404);
    }
});

Route::get('pAPI/labscoords', function () {
    $glabs = \App\Lab::select('id', 'name', 'slug',  'lon', 'lat')->where('status', '=', 'aktiv')->get();
    return  $glabs;
});

Route::get('fl/{id}', function ($id = 1) {
    if (Auth::user() && (Auth::user()->hasRole('admin') || session('is_admin'))) {
        Auth::logout();
        Auth::loginUsingId($id);
        //todo;check if the user lab hast membership 5
        $labs = User::find($id)->load('lab')->lab;
        if (count($labs) == 0) {
            $labs = User::find($id)->load('labs')->labs;
        }
        if (count($labs) > 0) {
            $lab = $labs[0];
            if ($lab->membership == 5)
                return redirect('/app/dentists');
        }

        return redirect('app');
    }

    return abort(404);
});

//Neues Labor anlegen

// Route::resource('lab', 'NewLabController');
Route::get('neues-labor', ['as' => 'lab.create', 'uses' => 'NewLabController@create']);
Route::post('labor', ['as' => 'lab.store', 'uses' => 'NewLabController@store']);
Route::get('labor/{name}', 'LabController@show');
// Route::get('labor/neu', ['as' => 'lab.create', 'uses' => 'LabController@newLab']);
// Route::post('labor', ['as' => 'lab.store', 'uses' => 'LabController@createNewLab']);

Route::resource('calendar', 'CalendarController');

Route::resource('dentistcalendar', 'DentistCalendarController');

Route::get('cron', 'PublicPageController@cron');
Route::get('scheduler', function () {
    if ($_SERVER['REMOTE_ADDR'] == '85.13.157.244' || \App::environment('local')) {
        \Artisan::call('schedule:run');
    }
});

//Helper

//Route::get('optimizephonenumbers', function () {
//    return abort(404);
//    $patients = App\PatientMeta::all();
//    foreach ($patients as $patient) {
//        $tel = $patient->tel;
//        $tel = preg_replace("/[^0-9]/", "", $tel);
//
//        $patient->zip = preg_replace("/[^0-9]/", "", $patient->zip);
//        if (!is_numeric($patient->zip)) {
//            $patient->zip = 'ungültig';
//        }
//        // echo $tel.'<br>';
//        try {
//            if (is_numeric($tel) && strlen($tel) < 15 && strlen($tel) > 6 && $tel != '' && strpos($tel, "00") !== 0) {
//                $patient->tel = @phone_format($tel, $country_code = 'DE');
//                $patient->save();
//            }
//        } catch (Exception $e) {
//            return [$patient->id, $tel, $e];
//        }
//    }
//});

Route::get('testform', 'PublicPageController@testform');
Route::post('testform', 'PublicPageController@test');

Route::get('/{lang?}', 'PublicPageController@startpage');

Route::group(['prefix' => 'api', 'namespace' => 'API'], function () {
    Route::resource('zipcode', 'ZipCodeController');
    Route::get('zipcode/getzipcodes/{country}', 'ZipCodeController@get');
    Route::resource('countries', 'CountriesController');
    Route::post('todo/toggleComplete', 'TodoController@toggleComplete');
    Route::post('todo/sort', 'TodoController@sort');
    Route::post('todo/sortWithWaiting', 'TodoController@sortWithWaiting');
    Route::post('todo/sortWithoutWaiting', 'TodoController@sortWithoutWaiting');
    Route::resource('todo', 'TodoController');
    Route::get('todosForContact/{contact}', 'TodoController@getTodoForContact');

    // New Dentist CRM TODOS
    Route::post('tododentist/toggleComplete', 'TodoDentistController@toggleComplete');
    Route::post('tododentist/sort', 'TodoDentistController@sort');
    Route::resource('tododentist', 'TodoDentistController');
    Route::get('todosdentistForContact/{contact}', 'TodoDentistController@getTodoForContact');
});

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware('auth');

Route::get('storage/{filename}', function ($filename)
{
    $path = storage_path('app/public/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
})->where(['filename' => '.*']);
