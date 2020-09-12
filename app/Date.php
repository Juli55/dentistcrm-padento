<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Date extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $appends = [
        'phase_label', 'phase_class',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dentist_contact()
    {
        return $this->belongsTo(DentistContact::class);
    }

    public function getPhaseLabelAttribute()
    {
        if(!$this->phase) return '';

        $phases = [
            0 => 'Alle',
            1 => 'Neu',
            2 => 'Kontaktaufnahme',
            3 => 'Labor-Termin vereinbart',
            4 => 'In Betreuung',
            5 => 'Auftrag erhalten',
            6 => 'Kein Interesse'
        ];

        $dentistPhases = [
            0 => 'Alle',
            1 => 'Fremd',
            2 => 'Vertrauen',
            3 => 'Beziehung',
            4 => 'Testkunde',
            5 => 'B-Kunde',
            6 => 'A-Kunde'
        ];

        if($this->patient_id) {
            return array_get($phases, $this->phase, '');
        }

        return array_get($dentistPhases, $this->phase, '');
    }

    public function getPhaseClassAttribute()
    {
        if(!$this->phase) return '';

        if($this->patient_id) {
            return 'stage label phase-' . $this->phase;
        }

        return 'stage label phasedentist-' . $this->phase;
    }
}
