<?php

namespace App\Observers;

use App\Models\StudySchedule;
use App\Models\Topic;

class TopicObserver
{
    public function created(Topic $topic): void
    {
        $startDate = request()->input('study_starts_at') ?: null;
        $nextReviewAt = $startDate ? now()->parse($startDate)->addDays(7) : now()->addDays(7);

        StudySchedule::create([
            'topic_id' => $topic->id,
            'next_review_at' => $nextReviewAt->toDateString(),
            'study_starts_at' => $startDate,
            'interval_days' => 7,
            'status' => 'pending',
        ]);
    }

    public function deleted(Topic $topic): void
    {
        $topic->studySchedule()->delete();
    }
}
