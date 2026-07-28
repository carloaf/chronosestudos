<?php

namespace App\Http\Controllers;

use App\Models\StudySchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $period = $request->get('period', '30d');

        $dateFrom = match ($period) {
            '7d' => Carbon::now()->subDays(7)->startOfDay(),
            '30d' => Carbon::now()->subDays(30)->startOfDay(),
            'month' => Carbon::now()->startOfMonth()->startOfDay(),
            'all' => Carbon::createFromDate(2020, 1, 1)->startOfDay(),
            default => Carbon::now()->subDays(30)->startOfDay(),
        };

        // All schedules for this user
        $schedules = StudySchedule::query()
            ->whereHas('topic.subject', fn($q) => $q->where('user_id', $user->id))
            ->with('topic.subject')
            ->get();

        // Reviewed in period
        $reviewedInPeriod = $schedules->filter(fn($s) =>
            $s->last_studied_at && $s->last_studied_at->between($dateFrom, Carbon::now())
        );

        // All user subjects with eager loading
        $subjects = $user->subjects()
            ->withCount('topics')
            ->with(['topics.studySchedule'])
            ->get();

        // Build per-subject report
        $subjectReports = $subjects->map(function ($subject) use ($dateFrom, $period) {
            $topicIds = $subject->topics->pluck('id');

            $allSchedules = StudySchedule::query()
                ->whereIn('topic_id', $topicIds)
                ->get();

            $totalTopics = $subject->topics->count();

            // Reviews: all-time count & in-period count
            $reviewsAllTime = $allSchedules->filter(fn($s) => $s->last_studied_at !== null)->count();
            $reviewsInPeriod = $allSchedules->filter(fn($s) =>
                $s->last_studied_at && $s->last_studied_at->between($dateFrom, Carbon::now())
            )->count();

            // Last review date for this subject
            $lastReview = $allSchedules
                ->filter(fn($s) => $s->last_studied_at !== null)
                ->sortByDesc('last_studied_at')
                ->first()?->last_studied_at;

            // Topics pending (never reviewed) 
            $pending = $allSchedules->filter(fn($s) => $s->last_studied_at === null)->count();

            // Questions stats
            $schedulesWithQuestions = $allSchedules->filter(fn($s) => $s->questions_total > 0);
            $questionsCorrect = $schedulesWithQuestions->sum('questions_correct');
            $questionsTotal = $schedulesWithQuestions->sum('questions_total');
            $accuracy = $questionsTotal > 0
                ? round(($questionsCorrect / $questionsTotal) * 100, 1)
                : null;

            return [
                'subject' => $subject,
                'totalTopics' => $totalTopics,
                'reviewsAllTime' => $reviewsAllTime,
                'reviewsInPeriod' => $reviewsInPeriod,
                'lastReview' => $lastReview,
                'pending' => $pending,
                'completionPercent' => $totalTopics > 0
                    ? round(($reviewsAllTime / $totalTopics) * 100)
                    : 0,
                'questionsCorrect' => $questionsCorrect,
                'questionsTotal' => $questionsTotal,
                'accuracy' => $accuracy,
            ];
        });

        // Daily review activity for chart (last 30 days or period-based)
        $chartDays = min(30, max(7, match ($period) {
            '7d' => 7,
            '30d' => 30,
            'month' => Carbon::now()->day,
            'all' => 30,
            default => 30,
        }));

        $dailyActivity = collect(range(0, $chartDays - 1))
            ->map(function ($daysAgo) use ($schedules) {
                $date = Carbon::now()->subDays($daysAgo)->toDateString();
                $count = $schedules->filter(fn($s) =>
                    $s->last_studied_at && $s->last_studied_at->toDateString() === $date
                )->count();
                return [
                    'date' => $date,
                    'day' => Carbon::parse($date)->format('d/m'),
                    'weekday' => Carbon::parse($date)->translatedFormat('D'),
                    'count' => $count,
                ];
            })
            ->reverse()
            ->values();

        // Summary stats
        $totalReviewsInPeriod = $reviewedInPeriod->count();
        $totalTopics = $schedules->count();
        $topicsReviewedAllTime = $schedules->filter(fn($s) => $s->last_studied_at !== null)->count();
        $activeDays = $dailyActivity->filter(fn($d) => $d['count'] > 0)->count();

        // Questions accuracy overall
        $schedulesWithQuestions = $schedules->filter(fn($s) => $s->questions_total > 0);
        $totalQuestionsCorrect = $schedulesWithQuestions->sum('questions_correct');
        $totalQuestionsTotal = $schedulesWithQuestions->sum('questions_total');
        $overallAccuracy = $totalQuestionsTotal > 0
            ? round(($totalQuestionsCorrect / $totalQuestionsTotal) * 100, 1)
            : null;

        // Recent activity (last 15 reviews)
        $recentActivity = $schedules
            ->filter(fn($s) => $s->last_studied_at !== null)
            ->sortByDesc('last_studied_at')
            ->take(15)
            ->values();

        return view('reports.index', compact(
            'period',
            'dateFrom',
            'subjectReports',
            'dailyActivity',
            'totalReviewsInPeriod',
            'totalTopics',
            'topicsReviewedAllTime',
            'activeDays',
            'chartDays',
            'recentActivity',
            'totalQuestionsCorrect',
            'totalQuestionsTotal',
            'overallAccuracy',
        ));
    }
}
