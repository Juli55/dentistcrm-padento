<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dentist extends Model
{
	public function dentmeta()
	{
		return $this->hasOne(DentMeta::class, 'dent_id', 'id');
	}

	public function labs()
	{
		return $this->belongsToMany(Lab::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
