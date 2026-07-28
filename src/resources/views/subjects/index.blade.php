<x-app-layout>
    <x-slot name="header">
        Disciplinas
    </x-slot>

    <div x-data="{ modalOpen: false, editSubject: null }">
        {{-- Header + add button --}}
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-slate-500">{{ $subjects->count() }} disciplina(s)</p>
            <button @click="modalOpen = true; editSubject = null"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nova Disciplina
            </button>
        </div>

        {{-- Subjects grid --}}
        @if($subjects->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
                <svg class="w-16 h-16 text-indigo-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <h2 class="text-xl font-semibold text-slate-700 mb-2">Nenhuma disciplina</h2>
                <p class="text-slate-500 mb-6">Crie sua primeira disciplina para organizar os estudos.</p>
                <button @click="modalOpen = true"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nova Disciplina
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($subjects as $subject)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow group">
                        {{-- Color stripe --}}
                        <div class="h-2" style="background-color: {{ $subject->color_code }}"></div>
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-3"
                                 x-data="{ favorited: {{ $subject->isFavorited() ? 'true' : 'false' }} }">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('subjects.show', $subject) }}" class="block group-hover:text-indigo-600 transition-colors">
                                        <h3 class="font-semibold text-slate-800 truncate">{{ $subject->name }}</h3>
                                    </a>
                                </div>
                                <div class="flex items-center gap-1 ml-2">
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
                                                               favoritable_type: 'App\\Models\\Subject',
                                                               favoritable_id: {{ $subject->id }}
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
                                    <button @click="modalOpen = true; editSubject = {{ $subject->toJson() }}"
                                            class="p-1.5 text-slate-300 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors"
                                            title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('subjects.destroy', $subject) }}"
                                          onsubmit="return confirm('Remover {{ addslashes($subject->name) }} e todos os seus tópicos?')">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 text-slate-300 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors"
                                                title="Remover">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 text-sm text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>{{ $subject->topics_count }} tópico(s)</span>
                            </div>

                            <a href="{{ route('subjects.show', $subject) }}"
                               class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                Ver tópicos
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Modal: Create/Edit Subject --}}
        <div x-show="modalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition.opacity>
            <div class="fixed inset-0 bg-black/40" @click="modalOpen = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6"
                 @click.outside="modalOpen = false">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">
                    <span x-text="editSubject ? 'Editar Disciplina' : 'Nova Disciplina'"></span>
                </h3>

                <form method="POST"
                      :action="editSubject ? '/subjects/' + editSubject.id : '{{ route('subjects.store') }}'"
                      class="space-y-4">
                    @csrf
                    <template x-if="editSubject">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nome</label>
                        <input type="text" name="name" required maxlength="255"
                               x-bind:value="editSubject ? editSubject.name : ''"
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5"
                               placeholder="Ex: Direito Constitucional">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Cor</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="color_code"
                                   x-bind:value="editSubject ? editSubject.color_code : '#4f46e5'"
                                   class="w-10 h-10 rounded-lg border-slate-300 cursor-pointer">
                            @foreach(['#4f46e5', '#0891b2', '#059669', '#d97706', '#dc2626', '#7c3aed', '#db2777'] as $color)
                                <button type="button"
                                        class="w-6 h-6 rounded-full border-2 border-white ring-1 ring-slate-200 hover:ring-indigo-400 cursor-pointer"
                                        style="background-color: {{ $color }}"
                                        onclick="this.form.color_code.value='{{ $color }}'"></button>
                            @endforeach
                        </div>
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
