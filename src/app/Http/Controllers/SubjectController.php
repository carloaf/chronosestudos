<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use App\Services\SpacedRepetitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function __construct(
        private SpacedRepetitionService $spacedRepetition
    ) {}
    public function index(Request $request): View
    {
        $subjects = $request->user()
            ->subjects()
            ->withCount('topics')
            ->latest()
            ->get();

        return view('subjects.index', compact('subjects'));
    }

    public function show(Request $request, Subject $subject): View
    {
        $this->authorize('view', $subject);

        $subject->loadCount('topics');
        $topics = $subject->topics()
            ->with('studySchedule')
            ->latest()
            ->get();

        // Mapear sugestões de intervalo para cada tópico
        $dateSuggestions = $topics->mapWithKeys(function ($topic) {
            $interval = $topic->studySchedule?->interval_days ?? 1;
            $nextInterval = $this->spacedRepetition->getNextInterval($interval);
            $suggestedDate = now()->addDays($nextInterval)->toDateString();
            
            return [$topic->id => [
                'interval' => $nextInterval,
                'date' => $suggestedDate,
            ]];
        });

        return view('subjects.show', compact('subject', 'topics', 'dateSuggestions'));
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        $request->user()->subjects()->create($request->validated());

        return redirect()->route('subjects.index')
            ->with('success', 'Disciplina criada com sucesso!');
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return redirect()->route('subjects.show', $subject)
            ->with('success', 'Disciplina atualizada!');
    }

    public function destroy(Request $request, Subject $subject): RedirectResponse
    {
        $this->authorize('delete', $subject);

        $subject->delete();

        return redirect()->route('subjects.index')
            ->with('success', 'Disciplina removida.');
    }
}
