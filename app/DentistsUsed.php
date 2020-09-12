<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DentistsUsed extends Model
{
    protected $table = 'dentists_used';

    protected $fillable = ['user_id', 'dentist_contact_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
