<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Topic extends Model
{
    protected $fillable = ['subject_id', 'title', 'notes'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function studySchedule()
    {
        return $this->hasOne(StudySchedule::class);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function isFavorited(): bool
    {
        return $this->favorites()->where('user_id', Auth::id())->exists();
    }
}
