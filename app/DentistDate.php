<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DentistDate extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }

    public function dentist()
    {
        return $this->belongsTo(DentistContact::class,'dentist_contact_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
