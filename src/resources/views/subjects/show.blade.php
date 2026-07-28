<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('subjects.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <span class="w-3 h-3 rounded-full" style="background-color: {{ $subject->color_code }}"></span>
            <span>{{ $subject->name }}</span>
        </div>
    </x-slot>

    <div x-data="{ 
        modalOpen: false, 
        editTopic: null,
        dateSuggestions: {{ json_encode($dateSuggestions ?? []) }}
    }">
        {{-- Subject info bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-500">{{ $subject->topics_count }} tópico(s)</span>
                <div class="flex items-center gap-1">
                    <button @click="modalOpen = true; editTopic = {title:'',notes:''}; $refs.topicForm.action='{{ route('subjects.topics.store', $subject) }}'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Novo Tópico
                    </button>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('subjects.destroy', $subject) }}"
                      onsubmit="return confirm('Remover esta disciplina e todos os seus tópicos?')">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1.5 text-sm text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                        Remover
                    </button>
                </form>
            </div>
        </div>

        {{-- Progress bar --}}
        @if(!$topics->isEmpty())
            @php
                $totalTopics = $topics->count();
                $completedTopics = $topics->where('studySchedule.status', 'completed')->count();
                $progressPercent = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100) : 0;
            @endphp
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-700">Progresso: {{ $completedTopics }}/{{ $totalTopics }}</span>
                    <span class="text-sm text-slate-500">{{ $progressPercent }}%</span>
                </div>
                <div class="h-3 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300"
                         style="width: {{ $progressPercent }}%; background: linear-gradient(to right, #4f46e5, #4338ca)">
                    </div>
                </div>
            </div>
        @endif

        {{-- Topics list --}}
        @if($topics->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
                <svg class="w-16 h-16 text-indigo-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <h2 class="text-lg font-semibold text-slate-700 mb-2">Nenhum tópico ainda</h2>
                <p class="text-slate-500 mb-6">Crie tópicos para organizar seu material de estudo.</p>
                <button @click="modalOpen = true"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Novo Tópico
                </button>
            </div>
        @else
            <div class="space-y-3">
                @foreach($topics as $topic)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-start justify-between gap-3"
                                 x-data="{ favorited: {{ $topic->isFavorited() ? 'true' : 'false' }} }">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('topics.show', [$subject, $topic]) }}"
                                       class="font-semibold text-slate-800 hover:text-indigo-600 transition-colors">
                                        {{ $topic->title }}
                                    </a>
                                    <p class="text-xs text-slate-400 mt-1">
                                        Criado em {{ $topic->created_at->format('d/m/Y') }}
                                        @if($topic->resources_count ?? false)
                                            · {{ $topic->resources_count }} recurso(s)
                                        @endif
                                    </p>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if($topic->studySchedule)
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                            {{ $topic->studySchedule->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                            {{ $topic->studySchedule->status === 'pending' ? 'Pendente' : 'Revisado' }}
                                        </span>
                                    @endif

                                    {{-- Favorite star --}}
                                    <button @click="favorited = !favorited;
                                                       fetch('{{ route('favorites.toggle') }}', {
                                                           method: 'POST',
                                                           headers: {
                                                               'Content-Type': 'application/json',
                                                               'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                               'Accept': 'application/json',
                                                           },
                                                           body: JSON.stringify({
                                                               favoritable_type: 'App\\Models\\Topic',
                                                               favoritable_id: {{ $topic->id }}
                                                           })
                                                       })"
                                            class="p-1.5 rounded-lg hover:bg-amber-50 transition-colors"
                                            :class="favorited ? 'text-amber-400' : 'text-slate-300 hover:text-amber-400'"
                                            title="Favoritar">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" stroke="none">
                                            <path x-show="favorited" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            <path x-show="!favorited" fill="none" stroke="currentColor" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </button>

                                    <button @click="editTopic = {id:{{ $topic->id }},title:{{ json_encode($topic->title) }},notes:{{ json_encode($topic->notes ?? '') }},study_starts_at:{{ json_encode($topic->studySchedule?->study_starts_at?->format('Y-m-d') ?? '') }}}; modalOpen = true; $refs.topicForm.action='{{ route('topics.update', [$topic]) }}'"
                                            class="p-1.5 text-slate-300 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('topics.destroy', [$topic]) }}"
                                          onsubmit="return confirm('Remover o tópico &quot;{{ addslashes($topic->title) }}&quot;?')">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 text-slate-300 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Modal: Create/Edit Topic --}}
        <div x-show="modalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition.opacity>
            <div class="fixed inset-0 bg-black/40" @click="modalOpen = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6"
                 @click.outside="modalOpen = false">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">
                    <span x-text="editTopic && editTopic.title ? 'Editar Tópico' : 'Novo Tópico'"></span>
                </h3>

                <form method="POST" x-ref="topicForm"
                      action="{{ route('subjects.topics.store', $subject) }}"
                      class="space-y-4">
                    @csrf
                    <template x-if="editTopic && editTopic.title">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Título</label>
                        <input type="text" name="title" required maxlength="255"
                               x-bind:value="editTopic ? editTopic.title : ''"
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5"
                               placeholder="Ex: Controle de Constitucionalidade">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Anotações (Markdown)</label>
                        <textarea name="notes" rows="4"
                                  x-bind:value="editTopic ? editTopic.notes || '' : ''"
                                  class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 font-mono"
                                  placeholder="# Título&#10;&#10;Conteúdo em **Markdown**..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Data de Início do Estudo (opcional)</label>
                        <input type="hidden" name="study_starts_at" value="">
                        <input type="text" id="dateDisplay" placeholder="dd/mm/aaaa"
                               x-effect="
                                 if (editTopic?.study_starts_at) {
                                     const parts = editTopic.study_starts_at.split('-');
                                     $el.value = parts[2] + '/' + parts[1] + '/' + parts[0];
                                 } else {
                                     $el.value = '';
                                 }
                               "
                               @input="
                                 const displayVal = $el.value;
                                 if (displayVal && displayVal.includes('/')) {
                                     const parts = displayVal.split('/');
                                     if (parts.length === 3) {
                                         const ymdFormat = parts[2] + '-' + parts[1] + '-' + parts[0];
                                         document.querySelector('input[name=study_starts_at]').value = ymdFormat;
                                         editTopic.study_starts_at = ymdFormat;
                                     }
                                 } else {
                                     document.querySelector('input[name=study_starts_at]').value = '';
                                     editTopic.study_starts_at = '';
                                 }
                               "
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                        
                        {{-- Smart date suggestions --}}
                        <template x-if="editTopic?.id && dateSuggestions && dateSuggestions[editTopic.id]">
                            <div class="mt-3 space-y-2">
                                <p class="text-xs text-slate-500 font-medium">Sugestões de data:</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="topic of Object.keys(dateSuggestions)" :key="topic">
                                        <template x-if="editTopic.id == topic && dateSuggestions[topic]">
                                            <div class="flex flex-wrap gap-1">
                                                <button type="button" 
                                                    @click="
                                                        const suggestion = dateSuggestions[editTopic.id];
                                                        const parts = suggestion.date.split('-');
                                                        const displayDate = parts[2] + '/' + parts[1] + '/' + parts[0];
                                                        document.getElementById('dateDisplay').value = displayDate;
                                                        document.querySelector('input[name=study_starts_at]').value = suggestion.date;
                                                        editTopic.study_starts_at = suggestion.date;
                                                    "
                                                    class="px-2 py-1 text-xs bg-indigo-50 text-indigo-700 rounded border border-indigo-200 hover:bg-indigo-100 transition-colors">
                                                    +<span x-text="dateSuggestions[editTopic.id].interval"></span> dias (<span x-text="new Date(dateSuggestions[editTopic.id].date).toLocaleDateString('pt-BR')"></span>)
                                                </button>
                                            </div>
                                        </template>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="modalOpen = false"
                                class="flex-1 px-4 py-2.5 border border-slate-300 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
