<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DentMeta extends Model
{
	public function dentist()
	{
		return $this->belongsTo(Dentist::class, 'dent_id', 'id');
	}
}
