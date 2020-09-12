<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timeframe extends Model
{
	use SoftDeletes;
	
	protected $fillable = ['lab_id', 'day_of_week', 'start', 'stop'];

	public function lab()
	{
		return $this->belongsTo(Lab::class);
	}
}
