<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('subjects.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">Disciplinas</a>
            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('subjects.show', $subject) }}" class="text-slate-400 hover:text-slate-600 transition-colors">{{ $subject->name }}</a>
            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-800 truncate">{{ $topic->title }}</span>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto" x-data="{ editingNotes: false }">
        {{-- Topic header --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $subject->color_code }}"></span>
                        <span class="text-xs text-slate-400">{{ $subject->name }}</span>
                    </div>
                    <h1 class="text-xl font-bold text-slate-800">{{ $topic->title }}</h1>
                </div>

                @if($topic->studySchedule)
                    <div class="text-right shrink-0">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full
                            {{ $topic->studySchedule->next_review_at <= now()->toDateString() ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $topic->studySchedule->next_review_at <= now()->toDateString() ? 'Pendente' : 'Revisado' }}
                        </span>
                        <p class="text-xs text-slate-400 mt-1">
                            Intervalo: {{ $topic->studySchedule->interval_days }}d
                        </p>
                    </div>
                @endif
            </div>

            {{-- Review actions --}}
            @if($topic->studySchedule && $topic->studySchedule->next_review_at <= now()->toDateString())
                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2">
                    @foreach(\App\Services\SpacedRepetitionService::INTERVALS as $interval)
                        <form method="POST" action="{{ route('schedules.review', $topic->studySchedule) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="interval_days" value="{{ $interval }}">
                            <button class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors
                                {{ $interval === $topic->studySchedule->interval_days ? 'bg-indigo-600 text-white border-indigo-600' : 'border-slate-200 text-slate-600 hover:border-indigo-300 hover:text-indigo-600' }}">
                                {{ $interval }}d
                            </button>
                        </form>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Notes (Markdown) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 sm:p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Anotações</h2>
                <button @click="editingNotes = !editingNotes"
                        class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                    <span x-text="editingNotes ? 'Visualizar' : 'Editar'"></span>
                </button>
            </div>

            {{-- View mode --}}
            <div x-show="!editingNotes">
                @if($notesHtml)
                    <div class="prose prose-slate max-w-none text-sm">
                        {!! $notesHtml !!}
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic">Nenhuma anotação. Clique em "Editar" para adicionar.</p>
                @endif
            </div>

            {{-- Edit mode: EasyMDE --}}
            <template x-if="editingNotes">
                <div x-init="
                    const container = $el;
                    $nextTick(() => {
                        if (typeof EasyMDE !== 'undefined' && !container.querySelector('.EasyMDEContainer')) {
                            new EasyMDE({
                                element: container.querySelector('textarea'),
                                spellChecker: false,
                                status: false,
                                toolbar: ['bold','italic','heading','|','quote','unordered-list','ordered-list','|','link','image','|','preview','side-by-side','fullscreen','|','guide'],
                                placeholder: 'Escreva suas anotações em Markdown...'
                            });
                        }
                    });
                ">
                    <form method="POST" action="{{ route('topics.update', [$topic]) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="title" value="{{ $topic->title }}">
                        <textarea name="notes">{{ $topic->notes }}</textarea>
                        <div class="flex gap-2 mt-3">
                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">
                                Salvar Anotações
                            </button>
                            <button type="button" @click="editingNotes = false"
                                    class="px-4 py-2 border border-slate-300 text-slate-600 rounded-xl text-sm font-medium hover:bg-slate-50 transition-colors">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </div>

        {{-- Resources --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 sm:p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Recursos</h2>
                <button onclick="document.getElementById('addResourceModal').classList.remove('hidden')"
                        class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                    + Adicionar
                </button>
            </div>

            @if($topic->resources->isEmpty())
                <p class="text-sm text-slate-400 italic">Nenhum recurso adicionado.</p>
            @else
                <div class="space-y-3">
                    @foreach($topic->resources as $resource)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div class="flex items-center gap-3 min-w-0">
                                {{-- Type icon --}}
                                <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                                    {{ $resource->type === 'youtube' ? 'bg-red-100 text-red-600' : '' }}
                                    {{ $resource->type === 'wikipedia' ? 'bg-slate-200 text-slate-600' : '' }}
                                    {{ $resource->type === 'link' ? 'bg-blue-100 text-blue-600' : '' }}">
                                    @if($resource->type === 'youtube')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                                    @elseif($resource->type === 'wikipedia')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2zm.04 4c-.48 0-.8.32-.8.8v5.2c0 .48.32.8.8.8h5.2c.48 0 .8-.32.8-.8v-5.2c0-.48-.32-.8-.8-.8h-5.2z"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    @endif
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ $resource->url }}" target="_blank" rel="noopener"
                                       class="text-sm font-medium text-indigo-600 hover:text-indigo-700 truncate block">
                                        {{ $resource->title ?? parse_url($resource->url, PHP_URL_HOST) }}
                                    </a>
                                    <span class="text-xs text-slate-400">{{ ucfirst($resource->type) }}</span>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('resources.destroy', $resource) }}" class="shrink-0 ml-2">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-slate-300 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Add resource modal --}}
            <div id="addResourceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/40" onclick="document.getElementById('addResourceModal').classList.add('hidden')"></div>
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Adicionar Recurso</h3>
                    <form method="POST" action="{{ route('topics.resources.store', $topic) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tipo</label>
                            <select name="type" required
                                    class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                                <option value="link">Link</option>
                                <option value="youtube">YouTube</option>
                                <option value="wikipedia">Wikipedia</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">URL</label>
                            <input type="url" name="url" required maxlength="2048"
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5"
                                   placeholder="https://...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Título (opcional)</label>
                            <input type="text" name="title" maxlength="255"
                                   class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5"
                                   placeholder="Nome descritivo do recurso">
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button"
                                    onclick="document.getElementById('addResourceModal').classList.add('hidden')"
                                    class="flex-1 px-4 py-2.5 border border-slate-300 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="flex-1 px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">
                                Adicionar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- YouTube Embeds --}}
        @php
            $youtubeResources = $topic->resources->where('type', 'youtube');
        @endphp
        @if($youtubeResources->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 sm:p-6">
                <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Vídeos</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($youtubeResources as $res)
                        @php
                            $videoId = null;
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $res->url, $matches)) {
                                $videoId = $matches[1];
                            }
                        @endphp
                        @if($videoId)
                            <div class="rounded-xl overflow-hidden bg-black aspect-video">
                                <iframe class="w-full h-full"
                                        src="https://www.youtube-nocookie.com/embed/{{ $videoId }}"
                                        frameborder="0" allowfullscreen
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                                </iframe>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- EasyMDE CDN --}}
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
    @endpush
    @push('scripts')
        <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
    @endpush
</x-app-layout>
