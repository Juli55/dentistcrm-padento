<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TodoDentist extends Model
{

    protected $table = 'dentist_todos';

    protected $fillable = [
        'title', 'is_queued', 'contact_id', 'order', 'creator_id'
    ];

    public function toggleComplete()
    {
        $this->completed_at = $this->isComplete() ? null : Carbon::now();

        $this->save();

        return $this;
    }

    public function isComplete()
    {
        return !! $this->completed_at;
    }

    public function dentist()
    {
        return $this->belongsTo(DentistContact::class, 'contact_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }
}
