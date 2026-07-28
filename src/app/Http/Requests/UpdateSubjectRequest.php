<?php

namespace App\Http\Requests;

use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        $subject = $this->route('subject');
        return $subject && $this->user()->id === $subject->user_id;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color_code' => ['nullable', 'string', 'size:7', 'starts_with:#'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da disciplina é obrigatório.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'color_code.size' => 'O código da cor deve ter 7 caracteres (ex: #4f46e5).',
            'color_code.starts_with' => 'O código da cor deve começar com #.',
        ];
    }
}
