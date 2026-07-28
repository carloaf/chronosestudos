<?php

namespace App\Http\Requests;

use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;

class StoreTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        $subject = $this->route('subject');
        return $subject && $this->user()->id === $subject->user_id;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:50000'],
            'study_starts_at' => ['nullable', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título do tópico é obrigatório.',
            'title.max' => 'O título deve ter no máximo 255 caracteres.',
            'notes.max' => 'As anotações são muito longas (máx. 50000 caracteres).',
            'study_starts_at.date_format' => 'Data inválida (formato: YYYY-MM-DD).',
        ];
    }
}
