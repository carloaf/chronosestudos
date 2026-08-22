<?php

namespace App\Http\Controllers;

use App\Services\SpacedRepetitionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private SpacedRepetitionService $spacedRepetition
    ) {}

    public function __invoke(Request $request): View
    {
        $user = $request->user();

        // Only reset schedules whose review date has passed back to pending
        $this->spacedRepetition->resetDueSchedules($user);

        // All schedules sorted by next_review_at ASC (earliest first)
        $allSchedules = $this->spacedRepetition->getPending($user)
            ->sortBy('next_review_at')
            ->values();

        // Only truly due today (next_review_at <= today, status = pending) — for summary card
        $dueTodayCount = $allSchedules
            ->filter(fn($s) => $s->next_review_at->toDateString() <= now()->toDateString() && $s->status === 'pending')
            ->count();

        // Next upcoming review date (from all schedules, not just pending)
        $nextUpcoming = \App\Models\StudySchedule::query()
            ->whereNotNull('study_starts_at')
            ->where('next_review_at', '>=', now()->toDateString())
            ->whereHas('topic.subject', fn($q) => $q->where('user_id', $user->id))
            ->orderBy('next_review_at')
            ->first();

        // Calculate progress based only on started topics (those with a study start date)
        $totalTopics = $allSchedules->count();

        $reviewedTopics = $allSchedules
            ->filter(fn($s) => $s->last_studied_at !== null)
            ->count();

        // Group schedules by times_studied for progress bars
        $timesStudiedGroups = $allSchedules
            ->groupBy('times_studied')
            ->map(fn($schedules, $level) => [
                'level' => (int) $level,
                'label' => $level == 0 ? 'Estudado' : "Revisão {$level}",
                'count' => $schedules->count(),
                'percent' => $totalTopics > 0 ? round(($schedules->count() / $totalTopics) * 100) : 0,
            ])
            ->sortBy('level')
            ->values();

        return view('dashboard', [
            'allSchedules' => $allSchedules,
            'dueTodayCount' => $dueTodayCount,
            'nextUpcoming' => $nextUpcoming,
            'totalSubjects' => $user->subjects()->count(),
            'totalTopics' => $totalTopics,
            'reviewedTopics' => $reviewedTopics,
            'timesStudiedGroups' => $timesStudiedGroups,
            'intervals' => SpacedRepetitionService::INTERVALS,
            'today' => now()->toDateString(),
        ]);
    }
}
