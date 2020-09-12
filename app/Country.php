<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'country_name', 'country_code'
    ];

    public function zip_codes()
    {
        return $this->hasMany(ZipCode::class);
    }
}
