<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabSetting extends Model
{
	use SoftDeletes;
	
	protected $fillable = [
		'name', 'value', 'second_value', 'lab_id', 'description', 'category',
	];

	public function lab()
	{
		return $this->hasOne(LabSetting::class);
	}
}
