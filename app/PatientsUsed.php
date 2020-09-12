<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PatientsUsed extends Model
{
	protected $table = 'patients_used';

	protected $fillable = ['user_id', 'patient_id'];

	public function lab()
	{
		return $this->belongsTo(Lab::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
