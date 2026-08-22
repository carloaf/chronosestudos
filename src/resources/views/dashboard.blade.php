<x-app-layout>
    <x-slot name="header">
        Revisões Pendentes
    </x-slot>

    <div>
        {{-- Summary cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <p class="text-sm text-slate-500 mb-1">A revisar hoje</p>
                <p class="text-3xl font-bold text-indigo-600">{{ $dueTodayCount }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <p class="text-sm text-slate-500 mb-1">Disciplinas</p>
                <p class="text-3xl font-bold text-slate-800">{{ $totalSubjects }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <p class="text-sm text-slate-500 mb-1">Próxima revisão</p>
                <p class="text-lg font-semibold text-slate-800">
                    @if($nextUpcoming)
                        {{ \Carbon\Carbon::parse($nextUpcoming->next_review_at)->format('d/m') }}
                    @else
                        —
                    @endif
                </p>
            </div>
        </div>

        {{-- Progress bars by study level --}}
        @if($timesStudiedGroups->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-8">
                <p class="text-sm font-medium text-slate-700 mb-4">Progresso de Estudos</p>
                <div class="space-y-4">
                    @foreach($timesStudiedGroups as $group)
                        @php
                            $pct = $group['percent'];
                            $barColor = $group['level'] == 0
                                ? 'linear-gradient(to right, #6366f1, #4f46e5)'
                                : 'linear-gradient(to right, #22c55e, #16a34a)';
                        @endphp
                        <div class="relative w-full bg-slate-200 rounded-full h-5 overflow-hidden">
                            {{-- Fill --}}
                            <div class="absolute inset-y-0 left-0 rounded-full transition-all duration-300"
                                 style="width: {{ $pct }}%; background: {{ $barColor }};"></div>
                            {{-- Dark text (unfilled portion) --}}
                            <div class="absolute inset-0 flex items-center justify-between px-2 text-[10px] font-medium text-slate-600">
                                <span>{{ $group['label'] }}</span>
                                <span>{{ $pct }}%</span>
                                <span>{{ $group['count'] }}/{{ $totalTopics }} tópicos</span>
                            </div>
                            {{-- White text (clipped to fill width) --}}
                            @if($pct > 0)
                            <div class="absolute inset-y-0 left-0 overflow-hidden rounded-full" style="width: {{ $pct }}%">
                                <div class="flex items-center justify-between px-2 text-[10px] font-medium text-white h-full whitespace-nowrap"
                                     style="width: {{ round(10000 / $pct) }}%">
                                    <span>{{ $group['label'] }}</span>
                                    <span>{{ $pct }}%</span>
                                    <span>{{ $group['count'] }}/{{ $totalTopics }} tópicos</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Reviews grouped by subject --}}
        @if($allSchedules->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
                <svg class="w-16 h-16 text-indigo-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h2 class="text-xl font-semibold text-slate-700 mb-2">Nenhuma revisão pendente!</h2>
                <p class="text-slate-500 mb-6">Crie tópicos em suas disciplinas para começar a estudar.</p>
                <a href="{{ route('subjects.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">
                    Ir para Disciplinas
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($allSchedules as $schedule)
                    @php
                        $subject = $schedule->topic->subject;
                        $nextReviewDate = \Carbon\Carbon::parse($schedule->next_review_at);
                    @endphp
                            <div class="bg-white rounded-xl shadow-sm border border-slate-200 hover:border-indigo-300 transition-colors"
                                 x-data="{
                                     favorited: {{ $schedule->topic->isFavorited() ? 'true' : 'false' }},
                                     reviewing: false,
                                     reviewError: '',
                                     reviewDate: '{{ $nextReviewDate->format('d/m/Y') }}',
                                     selectedInterval: 7,
                                     questionsCorrect: '',
                                     questionsTotal: '',
                                     reviewSaving: false,
                                     get dateSuggestions() {
                                         const parts = this.reviewDate.split('/');
                                         const baseDate = new Date(parts[2], parts[1] - 1, parts[0]);
                                         return [
                                             { days: 7, label: '+7 dias', date: this.getFormattedDate(new Date(baseDate.getTime() + 7 * 24 * 60 * 60 * 1000)) },
                                             { days: 14, label: '+14 dias', date: this.getFormattedDate(new Date(baseDate.getTime() + 14 * 24 * 60 * 60 * 1000)) },
                                             { days: 30, label: '+30 dias', date: this.getFormattedDate(new Date(baseDate.getTime() + 30 * 24 * 60 * 60 * 1000)) },
                                         ];
                                     },
                                     getFormattedDate(date) {
                                         const day = String(date.getDate()).padStart(2, '0');
                                         const month = String(date.getMonth() + 1).padStart(2, '0');
                                         const year = date.getFullYear();
                                         return day + '/' + month + '/' + year;
                                     }
                                 }">
                                {{-- Main row --}}
                                <div class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2.5 flex-wrap">
                                    {{-- Discipline color dot --}}
                                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $subject->color_code }}"></span>

                                    {{-- Discipline name --}}
                                    <span class="text-xs font-bold whitespace-nowrap" style="color: {{ $subject->color_code }}">{{ $subject->name }}</span>

                                    {{-- Title (flexible width) + favorite star --}}
                                    <span class="flex-1 min-w-[120px] flex items-center gap-1">
                                        <span class="text-sm font-medium text-slate-800 truncate" title="{{ $schedule->topic->title }}">
                                            {{ $schedule->topic->title }}
                                        </span>
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
                                                               favoritable_id: {{ $schedule->topic->id }}
                                                           })
                                                       })"
                                            class="shrink-0 hover:scale-110 transition-transform"
                                            :class="favorited ? 'text-amber-400' : 'text-slate-300 hover:text-amber-400'"
                                            title="Favoritar">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" stroke="none">
                                                <path x-show="favorited" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                <path x-show="!favorited" fill="none" stroke="currentColor" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        </button>
                                    </span>

                                    {{-- Study start date --}}
                                    <span class="text-xs text-slate-400 whitespace-nowrap">
                                        @if($schedule->study_starts_at)
                                            Início {{ \Carbon\Carbon::parse($schedule->study_starts_at)->format('d/m/Y') }}
                                        @endif
                                    </span>

                                    {{-- Last studied --}}
                                    <span class="text-xs text-slate-400 whitespace-nowrap">
                                        @if($schedule->last_studied_at)
                                            Últ. revisão {{ \Carbon\Carbon::parse($schedule->last_studied_at)->format('d/m/Y') }}
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </span>

                                    {{-- Next review date --}}
                                    <span class="text-xs text-indigo-600 whitespace-nowrap font-medium">
                                        📅 {{ $nextReviewDate->format('d/m/Y') }}
                                    </span>

                                    {{-- Times studied counter --}}
                                    <span class="text-xs font-bold whitespace-nowrap @if($schedule->last_studied_at) text-indigo-600 @else text-slate-300 @endif">
                                        {{ $schedule->times_studied }}
                                    </span>

                                    {{-- Actions --}}
                                    <div class="flex items-center gap-1">
                                        {{-- Mark as studied/reviewed: opens inline panel --}}
                                        <button @click="reviewing = !reviewing"
                                                class="px-2.5 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-medium hover:bg-indigo-700 transition-colors whitespace-nowrap">
                                            @if($schedule->last_studied_at)
                                                ✓ Revisado
                                            @else
                                                ✓ Estudado
                                            @endif
                                        </button>

                                        {{-- View topic --}}
                                        <a href="{{ route('topics.show', $schedule->topic) }}"
                                           class="px-2 py-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg text-xs transition-colors whitespace-nowrap">
                                            👁
                                        </a>
                                    </div>
                                </div>

                                {{-- Inline review form with date, acertos/de, inline error --}}
                                <div x-show="reviewing" x-cloak
                                     class="px-3 sm:px-4 pb-3 pt-2 border-t border-slate-100 bg-slate-50">
                                    {{-- Date field --}}
                                    <div class="flex items-center gap-2 mb-3 flex-wrap">
                                        <label class="text-xs text-slate-600 font-medium">Data:</label>
                                        <input type="text" x-model="reviewDate" placeholder="dd/mm/aaaa"
                                               class="text-xs border border-slate-300 rounded px-2 py-1.5 w-28 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                        <template x-for="suggestion in dateSuggestions" :key="suggestion.days">
                                            <button @click="reviewDate = suggestion.date"
                                                    class="px-2 py-1.5 text-xs bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded font-medium text-indigo-700 transition-colors whitespace-nowrap"
                                                    :title="suggestion.date">
                                                <span x-text="suggestion.label"></span>
                                            </button>
                                        </template>
                                    </div>

                                    {{-- Acertos de questões --}}
                                    <div class="flex items-center gap-2 mb-1">
                                        <label class="text-xs text-slate-600 font-medium whitespace-nowrap">Acertos:</label>
                                        <input type="number" x-model="questionsCorrect" min="0" max="9999" placeholder="8"
                                               class="text-xs border border-slate-300 rounded px-2 py-1.5 w-16 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                        <span class="text-xs text-slate-500">de:</span>
                                        <input type="number" x-model="questionsTotal" min="1" max="9999" placeholder="10"
                                               class="text-xs border border-slate-300 rounded px-2 py-1.5 w-16 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                        <span class="text-[10px] text-slate-400">questões</span>
                                    </div>

                                    {{-- Inline error --}}
                                    <p x-show="reviewError" x-text="reviewError" x-cloak
                                       class="text-xs text-red-500 mb-2"></p>

                                    <div class="flex items-center gap-2">
                                        <button @click="(async () => {
                                            reviewError = '';
                                            const correct = parseInt(questionsCorrect);
                                            const total = parseInt(questionsTotal);
                                            if (isNaN(correct) || correct < 0) {
                                                reviewError = 'Informe o número de acertos.';
                                                return;
                                            }
                                            if (isNaN(total) || total < 1) {
                                                reviewError = 'Informe o total de questões.';
                                                return;
                                            }
                                            reviewSaving = true;
                                            const formData = new FormData();
                                            formData.append('_token', '{{ csrf_token() }}');
                                            formData.append('_method', 'PATCH');
                                            formData.append('interval_days', selectedInterval);
                                            formData.append('questions_correct', correct);
                                            formData.append('questions_total', total);
                                            const parts = reviewDate.split('/');
                                            if (parts.length === 3) {
                                                formData.append('next_review_at', `${parts[2]}-${parts[1]}-${parts[0]}`);
                                            }
                                            try {
                                                const resp = await fetch('{{ route('schedules.review', $schedule) }}', {
                                                    method: 'POST',
                                                    body: formData,
                                                    headers: { 'Accept': 'application/json' },
                                                });
                                                if (resp.ok) {
                                                    window.location.reload();
                                                } else {
                                                    const data = await resp.json();
                                                    reviewError = data.message || 'Erro ao registrar revisão.';
                                                }
                                            } catch(e) {
                                                reviewError = 'Erro de conexão.';
                                            } finally {
                                                reviewSaving = false;
                                            }
                                        })()"
                                                :disabled="reviewSaving"
                                                class="px-3 py-1.5 bg-indigo-600 text-white rounded text-xs font-medium hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                                                x-text="reviewSaving ? 'Salvando...' : 'Salvar revisão'">
                                        </button>
                                        <button @click="reviewing = false; reviewError = ''"
                                                class="px-3 py-1.5 text-slate-500 hover:text-slate-700 rounded text-xs transition-colors">
                                            Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
