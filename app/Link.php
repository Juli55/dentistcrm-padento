<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Link extends Model
{
    protected $fillable = [
        'parent_id', 'title', 'url'
    ];

    protected $appends = [
        'full_url'
    ];

    function getFullUrlAttribute()
    {
        return url($this->url);
    }
}
