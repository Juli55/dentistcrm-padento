<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PatientProp extends Model
{
	protected $fillable = ['value'];

	public function pivot()
	{
		return $this->belongsToMany(Patient::class)->withPivot('value', 'id');
	}

	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}
}
