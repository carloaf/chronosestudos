<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Subject extends Model
{
    protected $fillable = ['user_id', 'name', 'color_code'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
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
