<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PropPivot extends Model
{
	protected $table = 'patient_patient_prop';

	protected $fillable = ['value', 'patient_id', 'patient_prop_id', 'status'];
}
