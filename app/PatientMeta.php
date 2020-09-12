<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientMeta extends Model
{
	use SoftDeletes;

	protected $dates = ['deleted_at'];

	public function patient()
	{
		return $this->belongsTo(Patient::class);
	}
}
