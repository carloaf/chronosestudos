<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = ['topic_id', 'type', 'url', 'title'];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
