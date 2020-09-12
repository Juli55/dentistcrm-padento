<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DentistContactMeta extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function dentist()
    {
        return $this->belongsTo(DentistContact::class);
    }
}
