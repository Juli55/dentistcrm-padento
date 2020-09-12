<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\UserLogger',
        ],
        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\UserLoggedOut'
        ],
        'App\Events\PropertiesChanged' => [
            'App\Listeners\SyncProperties'
        ],
        'App\Events\UserStatusChanged' => [
            'App\Listeners\ChangedUserStatus'
        ],
        'App\Events\PatientFormFilled' => [
            'App\Listeners\NewPatientRequest'
        ],
        'App\Events\PatientPhaseChanged' => [
            'App\Listeners\PatientPhaseChange'
        ],
        'App\Events\SystemSettingChanged' => [
            'App\Listeners\SystemLogger'
        ],
        'App\Events\NoLabWasFound' => [
            'App\Listeners\NoLabFound'
        ],
        'App\Events\PatientDeleted' => [
            'App\Listeners\PatientWasDeleted'
        ],
        'App\Events\PatientMovedBack' => [
            'App\Listeners\PatientWasMovedBack'
        ],
        'App\Events\PlzIsMissing' => [
            'App\Listeners\PlzMissing'
        ],
        'App\Events\PatientConfirmed' => [
            'App\Listeners\PatientIsConfirmed'
        ],
        'App\Events\PatientReferred' => [
            'App\Listeners\PatientWasReferred'
        ],
        'App\Events\PatientUnobtainable' => [
            'App\Listeners\PatientWasUnobtainable'
        ],
        'App\Events\PatientSelfContacted' => [
            'App\Listeners\PatientWasSelfContacted'
        ],
        'App\Events\NewPatientDate' => [
            'App\Listeners\NewPatientDateStored'
        ],
        'App\Events\MailError' => [
            'App\Listeners\MailErrorListener'
        ],
        'App\Events\TimeframesMissing' => [
            'App\Listeners\MissingTimeframes'
        ],
        'App\Events\PatientHasBeenApproved' => [
            'App\Listeners\PatientWasApproved'
        ],
        'App\Events\LabMembershipChange' => [
            'App\Listeners\LabMembershipChanged'
        ],
        'App\Events\ValidationFailed' => [
            'App\Listeners\SendErrorNotification'
        ],
        'App\Events\NewDentistDate' => [
            'App\Listeners\NewDentistDateStored'
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);
    }
}
