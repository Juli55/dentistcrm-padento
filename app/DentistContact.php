<?php

namespace App;

use Carbon\Carbon;
use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DentistContact extends Model {
    use SoftDeletes, Eloquence;

    protected $appends = [
        'phase_label',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($dentist) {
            $dentist->dates()->delete();
            $dentist->employeeDates()->delete();
            $dentist->dentistmeta()->delete();
        });

    }


    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function dentistmeta()
    {
        return $this->hasOne(DentistContactMeta::class);
    }

    public function props()
    {
        return $this->belongsToMany(PatientProp::class)->withPivot('value', 'id');
    }

    public function notes()
    {
        return $this->hasMany(DentistNote::class);
    }

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }

    public function usedpatient()
    {
        return $this->belongsTo(PatientsUsed::class);
    }

    public function dates()
    {
        return $this->hasMany(Date::class)->orderBy('date');
    }

    public function phaseDate()
    {
        return $this->hasMany(Date::class)->orderBy('date')->where('phase', $this->phase);
    }

    public function employeeDates()
    {
        return $this->hasMany(EmployeeDate::class, 'dentist_contact_id');
    }

    public function nextDate()
    {
        return $this->dates()->where('date', '>', Carbon::now())->orderBy('date', 'ASC');
    }

    public function latestDate()
    {
        return $this->hasOne(DentistDate::class)->orderBy('date', 'desc');
    }

    public function latestEmployeeDate()
    {
        return $this->hasOne(EmployeeDate::class)->orderBy('date', 'desc');
    }

    public function lastDate()
    {
        return $this->hasOne(DentistDate::class)->latest();
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function tasks()
    {
        return $this->hasMany(TodoDentist::class, 'contact_id')->orderBy('order');
    }

    public function removeDentistDates($excepts = [])
    {
        $this->dates()->whereNotNull('dentist_contact_id')->where('date', ' >= ', Carbon::now())->whereNotIn('id', $excepts)->delete();
    }

    public function getPhaseLabelAttribute()
    {
        $phases = [
            0 => 'Alle',
            1 => 'Fremd',
            2 => 'Vertrauen',
            3 => 'Beziehung',
            4 => 'Testkunde',
            5 => 'B-Kunde',
            6 => 'A-Kunde'
        ];

        return array_get($phases, $this->phase, '');
    }
}

