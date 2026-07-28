<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewScheduleRequest;
use App\Models\StudySchedule;
use App\Services\SpacedRepetitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StudyScheduleController extends Controller
{
    public function __construct(
        private SpacedRepetitionService $spacedRepetition
    ) {}

    public function review(ReviewScheduleRequest $request, StudySchedule $schedule): RedirectResponse
    {
        $this->spacedRepetition->markAsReviewed(
            $schedule,
            $request->validated('interval_days'),
            $request->validated('questions_correct'),
            $request->validated('questions_total'),
            $request->validated('next_review_at')
        );
        
        $schedule->refresh();

        return redirect()->route('dashboard')
            ->with('success', 'Revisão registrada! Próxima: ' . \Carbon\Carbon::parse($schedule->next_review_at)->format('d/m/Y'));
    }

    public function updateInterval(ReviewScheduleRequest $request, StudySchedule $schedule): RedirectResponse
    {
        $schedule->update(['interval_days' => $request->validated('interval_days')]);

        return redirect()->back()
            ->with('success', 'Intervalo atualizado para ' . $request->validated('interval_days') . ' dias.');
    }

    public function updateNextReview(Request $request, StudySchedule $schedule): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'next_review_at' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'next_review_at.required' => 'A data de revisão é obrigatória.',
            'next_review_at.date' => 'Informe uma data válida.',
            'next_review_at.after_or_equal' => 'A data deve ser hoje ou uma data futura.',
        ]);

        $schedule->update(['next_review_at' => $validated['next_review_at']]);

        return response()->json(['message' => 'Data de revisão atualizada.']);
    }
}
