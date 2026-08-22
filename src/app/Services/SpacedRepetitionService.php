<?php

namespace App\Services;

use App\Models\StudySchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class SpacedRepetitionService
{
    public const INTERVALS = [1, 7, 15, 30, 45];

    public function getNextInterval(int $currentIntervalDays): int
    {
        $currentIndex = array_search($currentIntervalDays, self::INTERVALS);
        
        if ($currentIndex === false || $currentIndex >= count(self::INTERVALS) - 1) {
            return self::INTERVALS[count(self::INTERVALS) - 1];
        }
        
        return self::INTERVALS[$currentIndex + 1];
    }

    public function markAsReviewed(StudySchedule $schedule, int $nextIntervalDays, int $questionsCorrect, int $questionsTotal, ?string $nextReviewAt = null): void
    {
        $schedule->update([
            'last_studied_at' => now(),
            'next_review_at' => $nextReviewAt ?? now()->addDays($nextIntervalDays)->toDateString(),
            'interval_days' => $nextIntervalDays,
            'questions_correct' => $questionsCorrect,
            'questions_total' => $questionsTotal,
            'times_studied' => $schedule->times_studied + 1,
            'status' => 'completed',
        ]);
    }

    public function getDueToday(User $user): Collection
    {
        return StudySchedule::query()
            ->where('next_review_at', '<=', now()->toDateString())
            ->where('status', 'pending')
            ->whereNotNull('study_starts_at')
            ->whereHas('topic.subject', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['topic.subject', 'topic.resources'])
            ->orderBy('next_review_at')
            ->get();
    }

    public function getPending(User $user): Collection
    {
        return StudySchedule::query()
            ->whereNotNull('study_starts_at')
            ->whereHas('topic.subject', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['topic.subject', 'topic.resources'])
            ->orderBy('next_review_at')
            ->get();
    }

    public function resetDueSchedules(User $user): int
    {
        return StudySchedule::query()
            ->where('next_review_at', '<=', now()->toDateString())
            ->where('status', 'completed')
            ->whereHas('topic.subject', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->update(['status' => 'pending']);
    }
}
