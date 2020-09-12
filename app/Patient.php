<?php

namespace App;

use Carbon\Carbon;
use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Patient extends Model
{
    use SoftDeletes, Eloquence, LogsActivity;

    protected static $recordEvents = [];

    // protected $maps = [
    //     'dates' => 'date',
    // ];

    protected $fillable = [
        'queued', 'archived', 'requested_deletation_at', 'accepted_dsgvo_at', 'accepted_dsgvo_text',
        'estimated_deletation_at',
    ];

    protected $appends = [
        'phase_label',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($patient) {
            $patient->dates()->forceDelete();
            $patient->employeeDates()->forceDelete();
            $patient->patientmeta()->forceDelete();
            $patient->attachments->each(function ($attachment) { $attachment->forceDelete(); });
            $patient->activity()->delete();
        });

        // static::restored(function ($patient) {
        //     $patient->dates()->restore();
        //     $patient->employeeDates()->restore();
        //     $patient->patientmeta()->restore();
        // });
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function patientmeta()
    {
        return $this->hasOne(PatientMeta::class);
    }

    public function props()
    {
        return $this->belongsToMany(PatientProp::class)->withPivot('value', 'id');
    }

    public function notes()
    {
        return $this->hasMany(PatientNote::class);
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

    public function employeeDates()
    {
        return $this->hasMany(EmployeeDate::class, 'patient_id');
    }

    public function nextDate()
    {
        return $this->dates()->where('date', '>', Carbon::now())->orderBy('date', 'ASC');
    }

    public function next_date()
    {
        return $this->hasOne(Date::class)->where('date', '>', Carbon::now())->orderBy('date');
    }

    public function next_employee_date()
    {
        return $this->hasOne(EmployeeDate::class, 'patient_id')->where('date', '>', Carbon::now())->orderBy('date');
    }

    public function latestDate()
    {
        return $this->hasOne(Date::class)->orderBy('date', 'desc');
    }

    public function latestEmployeeDate()
    {
        return $this->hasOne(EmployeeDate::class)->orderBy('date', 'desc');
    }

    public function lastDate()
    {
        return $this->hasOne(Date::class)->latest();
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function tasks()
    {
        return $this->hasMany(Todo::class, 'contact_id')->orderBy('order');
    }

    public function archive()
    {
        if (!$this->archived) {
            $this->update(['archived' => 1]);

            activity()->causedBy(auth()->user())->performedOn($this)->log('archived');
        }
    }

    public function unarchive()
    {
        if ($this->archived) {
            $this->update(['archived' => 0]);

            activity()->causedBy(auth()->user())->performedOn($this)->log('unarchived');
        }
    }

    public function queue()
    {
        if (!$this->queued) {
            $this->update(['queued' => 1]);

            activity()->causedBy(auth()->user())->performedOn($this)->log('queued');
        }
    }

    public function unqueue()
    {
        if ($this->queued) {
            $this->update(['queued' => 0]);

            activity()->causedBy(auth()->user())->performedOn($this)->log('unqueued');
        }
    }

    public function removeDates()
    {
        $this->removeContactDates();
    }

    public function removeContactDates($excepts = [])
    {
        $this->removeEmployeeDates();

        $this->dates()->whereNotNull('patient_id')->where('date', '>=', Carbon::now())->whereNotIn('id', $excepts)->delete();
    }

    public function removeEmployeeDates()
    {
        if ($this->employeeDates()->count()) {
            activity()->causedBy(adminUser())->performedOn($this)->log('employee_dates_deleted');

            $this->employeeDates()->delete();
        }
    }

    public function moveToLab(Lab $lab, $queue = true)
    {
        $this->removeEmployeeDates();

        if ($lab->isQueueLab() && $queue && ($this->lab_id != $lab->id)) {
            $this->queue();
        } else {
            $this->unqueue();
        }

        if (!$this->confirmed && $this->lab_id == $lab->id) {
            $this->archive();
        }

        $this->lab_id = $lab->id;

        $this->save();
    }

    public function createToDos()
    {
        $todos = Todo::whereNull('contact_id')->where('is_queued', $this->queued)->get();

        foreach ($todos as $todoItem) {
            $todo             = new Todo();
            $todo->contact_id = $this->id;
            $todo->title      = $todoItem->title;
            $todo->is_queued  = $todoItem->is_queued;
            $todo->order      = $todoItem->order;
            $todo->creator_id = $todoItem->creator_id;
            $todo->save();
        }
    }

    public function getPhaseLabelAttribute()
    {
        $phases = [
            0 => 'Alle',
            1 => 'Neu',
            2 => 'Kontaktaufnahme',
            3 => 'Labor-Termin vereinbart',
            4 => 'In Betreuung',
            5 => 'Auftrag erhalten',
            6 => 'Kein Interesse',
        ];

        return array_get($phases, $this->phase, '');
    }

    public function requested_deletation()
    {
        $this->requested_deletation_at = Carbon::now();

        $this->save();
    }

    public function acceptDsgvo()
    {
        $this->accepted_dsgvo_at = Carbon::now();
        $this->accepted_dsgvo_text = config('padento.dsgvo.accepted_text');

        $this->save();
    }
}

