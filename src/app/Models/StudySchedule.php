<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudySchedule extends Model
{
    protected $fillable = ['topic_id', 'last_studied_at', 'next_review_at', 'interval_days', 'questions_correct', 'questions_total', 'times_studied', 'status', 'study_starts_at'];

    protected $casts = [
        'last_studied_at' => 'datetime',
        'next_review_at' => 'date',
        'study_starts_at' => 'date',
        'times_studied' => 'integer',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
