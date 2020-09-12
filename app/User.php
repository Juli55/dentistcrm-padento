<?php

namespace App;

use Carbon\Carbon;
use Spatie\Activitylog\Traits\CausesActivity;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use EntrustUserTrait, CausesActivity;

    protected $fillable = [
        'name', 'email', 'password', 'accepted_dsgvo_at', 'accepted_dsgvo_text',
    ];

    protected $hidden = [
        'remember_token',
    ];

    public function metas()
    {
        return $this->hasOne(UserMeta::class);
    }

    public function labs()
    {
        return $this->belongsToMany(Lab::class)->with('settings')->with('timeframes');
    }

    public function usedpatients()
    {
        return $this->belongsToMany(PatientsUsed::class);
    }

    public function lab()
    {
        return $this->hasMany(Lab::class)->with('settings')->with('timeframes');
    }

    public function labsettings()
    {
        return $this->hasMany(LabSetting::class);
    }

    public function acceptDsgvo()
    {
        $this->accepted_dsgvo_at = Carbon::now();
        $this->accepted_dsgvo_text = config('padento.dsgvo.accepted_text');

        $this->save();
    }
}
