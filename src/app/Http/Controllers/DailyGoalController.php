<?php

namespace App\Http\Controllers;

use App\Services\SpacedRepetitionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyGoalController extends Controller
{
    public function __construct(
        private SpacedRepetitionService $spacedRepetition
    ) {}

    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $this->spacedRepetition->resetDueSchedules($user);

        $dueToday = $this->spacedRepetition->getDueToday($user)
            ->sortBy('next_review_at')
            ->values();

        return view('daily-goal', [
            'dueToday' => $dueToday,
            'today' => now()->toDateString(),
        ]);
    }
}
