<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LabSlug extends Model
{
    protected $fillable = [
        'lab_id', 'slug',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }
}
