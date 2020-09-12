<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZipCode extends Model
{
    protected $fillable = [
        'zip_code', 'country_id'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}


