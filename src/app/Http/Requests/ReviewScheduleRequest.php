<?php

namespace App\Http\Requests;

use App\Models\StudySchedule;
use Illuminate\Foundation\Http\FormRequest;

class ReviewScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $schedule = $this->route('schedule');
        return $schedule && $this->user()->id === $schedule->topic->subject->user_id;
    }

    public function rules(): array
    {
        return [
            'interval_days' => ['required', 'integer', 'min:1', 'max:365'],
            'questions_correct' => ['required', 'integer', 'min:0', 'max:9999'],
            'questions_total' => ['required', 'integer', 'min:1', 'max:9999'],
            'next_review_at' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'interval_days.required' => 'O intervalo em dias é obrigatório.',
            'interval_days.integer' => 'O intervalo deve ser um número inteiro.',
            'interval_days.min' => 'O intervalo mínimo é 1 dia.',
            'interval_days.max' => 'O intervalo máximo é 365 dias.',
            'questions_correct.required' => 'Informe o número de acertos.',
            'questions_correct.integer' => 'O número de acertos deve ser um número inteiro.',
            'questions_correct.min' => 'O número de acertos não pode ser negativo.',
            'questions_correct.max' => 'O número máximo de acertos é 9999.',
            'questions_total.required' => 'Informe o total de questões.',
            'questions_total.integer' => 'O total de questões deve ser um número inteiro.',
            'questions_total.min' => 'O total de questões deve ser pelo menos 1.',
            'questions_total.max' => 'O total máximo de questões é 9999.',
            'next_review_at.date' => 'Informe uma data válida.',
            'next_review_at.after_or_equal' => 'A data deve ser hoje ou futura.',
        ];
    }
}
