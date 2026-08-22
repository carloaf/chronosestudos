<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Models\Subject;
use App\Models\Topic;
use App\Services\MarkdownSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function __construct(
        private MarkdownSanitizer $sanitizer,
    ) {}

    public function show(Topic $topic): View
    {
        $this->authorize('view', $topic);

        $topic->load(['resources', 'studySchedule']);

        return view('topics.show', [
            'subject' => $topic->subject,
            'topic' => $topic,
            'notesHtml' => $topic->notes
                ? $this->sanitizer->render($topic->notes)
                : null,
        ]);
    }

    public function store(StoreTopicRequest $request, Subject $subject): RedirectResponse
    {
        $subject->topics()->create($request->validated());

        return redirect()->route('subjects.show', $subject)
            ->with('success', 'Tópico criado! Agende sua primeira revisão.');
    }

    public function update(UpdateTopicRequest $request, Topic $topic): RedirectResponse
    {
        $validated = $request->validated();
        
        // Atualizar Topic
        $topic->update([
            'title' => $validated['title'],
            'notes' => $validated['notes'],
        ]);

        // Atualizar a data de início do estudo no agendamento (permite limpar o campo)
        $schedule = $topic->studySchedule;
        $newStartDate = $validated['study_starts_at'] ?? null;
        $newStartDate = $newStartDate ?: null;

        if ($schedule) {
            $previousStartDate = $schedule->study_starts_at?->toDateString();

            $schedule->study_starts_at = $newStartDate;

            // Recalcula a próxima revisão quando a data de início muda e o tópico ainda não foi revisado.
            if ($newStartDate && $newStartDate !== $previousStartDate && (int) $schedule->times_studied === 0) {
                $schedule->next_review_at = now()->parse($newStartDate)->addDays(7)->toDateString();
            }

            $schedule->save();
        } else {
            $topic->studySchedule()->create([
                'study_starts_at' => $newStartDate,
                'next_review_at' => $newStartDate
                    ? now()->parse($newStartDate)->addDays(7)->toDateString()
                    : now()->addDays(7)->toDateString(),
                'interval_days' => 7,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('topics.show', $topic)
            ->with('success', 'Tópico atualizado!');
    }

    public function destroy(Topic $topic): RedirectResponse
    {
        $this->authorize('delete', $topic);

        $subject = $topic->subject;

        $topic->delete();

        return redirect()->route('subjects.show', $subject)
            ->with('success', 'Tópico removido.');
    }
}
