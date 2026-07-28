<?php

namespace App\Http\Requests;

use App\Models\Topic;
use Illuminate\Foundation\Http\FormRequest;

class StoreResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $topic = $this->route('topic');
        return $topic && $this->user()->id === $topic->subject->user_id;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:youtube,wikipedia,link'],
            'url' => ['required', 'url', 'max:2048'],
            'title' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'O tipo de recurso é obrigatório.',
            'type.in' => 'Tipo inválido. Use: YouTube, Wikipedia ou Link.',
            'url.required' => 'A URL do recurso é obrigatória.',
            'url.url' => 'Informe uma URL válida.',
            'url.max' => 'A URL é muito longa (máx. 2048 caracteres).',
            'title.max' => 'O título deve ter no máximo 255 caracteres.',
        ];
    }
}
