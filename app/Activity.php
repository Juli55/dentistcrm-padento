<?php

namespace App;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{

    protected $appends = [
        'causer_name',
        'visibility',
    ];

    public function getCauserNameAttribute()
    {
        $systemActivities = [
            'employee_dates_deleted',
        ];

        if ($this->getExtraProperty('causedBySystem') == true || in_array($this->description, $systemActivities)) {
            return 'System';
        }

        $causer = $this->causer;

        if ($causer instanceof Patient) {
            return $causer->patientmeta->name;
        }

        return $causer ? $causer->name : '';
    }

    public function getVisibilityAttribute()
    {
        if(auth()->user()->hasRole('admin')) {
            return true;
        }

        $exclude = [
            'employee_date_added',
            'employee_dates_deleted',
            'employee_date_deleted'
        ];

        if (in_array($this->description, $exclude)) {
            return false;
        }

        return true;
    }
}
