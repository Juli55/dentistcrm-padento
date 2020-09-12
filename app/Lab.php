<?php

namespace App;

use \Carbon\Carbon;
use App\Traits\Imageable;
use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lab extends Model
{
    use Eloquence, SoftDeletes, Imageable;

    protected $fillable = [
        'name',
        'status',
        'google_city',
        'slug',
        'directtoken',
        'lat',
        'lon',
        'user_id',
        'has_multi_user',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($lab) {
            $lab->dates()->delete();
            $lab->labmeta()->delete();
            $lab->patients()->delete();
            $lab->settings()->delete();
            $lab->timeframes()->delete();
            $lab->slugs()->delete();
            $lab->removeImages();
        });
    }

    public function labmeta()
    {
        return $this->hasOne(LabMeta::class);
    }

    /*public function images()
    {
        return $this->hasOne(LabImage::class);
    }*/

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class)->with('nextdate');
    }

    public function dentistContact()
    {
        return $this->hasMany(DentistContact::class)->with('nextdate');
    }

    public function slugs()
    {
        return $this->hasMany(LabSlug::class);
    }

    public function dates()
    {
        return $this->hasMany(Date::class)->with('patient.patientmeta', 'dentist_contact.dentistmeta');
    }

    public function latestdates()
    {
        return $this->hasMany(Date::class);
    }

    public function settings()
    {
        return $this->hasMany(LabSetting::class);
    }

    public function timeframes()
    {
        return $this->hasMany(Timeframe::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktiv');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inaktiv');
    }

    public function allPatientCount()
    {
        return $this->patients()
            ->selectRaw('lab_id, count(*) as aggregate')
            ->groupBy('lab_id');
    }

    public function thirtyDaysPatientCount()
    {
        return $this->patients()
            ->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])
            ->selectRaw('lab_id, count(*) as aggregate')
            ->groupBy('lab_id');
    }

    #   $month = $this->patients()->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()])->count();

    public function sevenDaysPatientCount()
    {
        return $this->patients()
            ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
            ->selectRaw('lab_id, count(*) as aggregate')
            ->groupBy('lab_id');
    }

    public function scopeQueued($query)
    {
        return $query->where(function ($q) {
            $q->where('membership', 1)->orWhere('membership', 4);
        });
    }

    public function scopeNotQueued($query)
    {
        return $query->where(function ($q) {
            $q->where('membership', '<>', 1)->orWhere('membership', '<>', 4);
        });
    }

    public function isQueueLab()
    {
        return !!($this->membership == 1 || $this->membership == 4);
    }

    public function saveSlug()
    {
        if(!$this->slugs()->where('slug', $this->slug)->exists()) {
            $this->slugs()->create(['slug' => $this->slug]);
        }
    }

    # $sevenDays = $this->patients()->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
    #$user_id = $this->user->id;
    #$logins = Activity::with('user')->where('user_id', '=', $user_id)->where('text', 'LIKE', '%angemeldet%')->latest()->limit(100)->count();
    #  $dates = $this->dates()->count();

#        return ['all' => $all, 'month' => $month, 'thirtydays' => $thirtyDays, 'sevendays' => $sevenDays, 'logins' => $logins, 'dates' => $dates];
    #}
}
