<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabMeta extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'hello',
        'contact_person',
        'special1',
        'special2',
        'special3',
        'special4',
        'special5',
        'text',
        'contact_email',
        'tel',
        'street',
        'city',
        'zip',
        'country_code',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }
}
