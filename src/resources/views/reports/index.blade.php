<x-app-layout>
    <x-slot name="header">
        Relatórios
    </x-slot>

    <div x-data="{ period: '{{ $period }}' }">
        {{-- Period filter tabs --}}
        <div class="flex items-center gap-1.5 mb-6 flex-wrap">
            <span class="text-sm text-slate-500 mr-1">Período:</span>
            @foreach([
                ['7d', '7 dias'],
                ['30d', '30 dias'],
                ['month', 'Este mês'],
                ['all', 'Todo período'],
            ] as [$val, $label])
                <a href="{{ route('reports.index', array_merge(request()->except('period'), ['period' => $val])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors
                          {{ $period === $val ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Summary cards --}}
        <div class="flex flex-wrap gap-3 mb-6">
            <div class="w-[calc(50%-6px)] sm:w-[calc(25%-9px)] bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Revisões no período</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $totalReviewsInPeriod }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $dateFrom->format('d/m/Y') }} — hoje</p>
            </div>
            <div class="w-[calc(50%-6px)] sm:w-[calc(25%-9px)] bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Tópicos revisados</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $topicsReviewedAllTime }}</p>
                <p class="text-xs text-slate-400 mt-1">de {{ $totalTopics }} total</p>
            </div>
            <div class="w-[calc(50%-6px)] sm:w-[calc(25%-9px)] bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Dias ativos</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $activeDays }}</p>
                <p class="text-xs text-slate-400 mt-1">no gráfico abaixo</p>
            </div>
            <div class="w-[calc(50%-6px)] sm:w-[calc(25%-9px)] bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Acertos</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">
                    {{ $overallAccuracy !== null ? $overallAccuracy . '%' : '—' }}
                </p>
                <p class="text-xs text-slate-400 mt-1">
                    {{ $totalQuestionsCorrect }}/{{ $totalQuestionsTotal }} questões
                </p>
            </div>
        </div>

        {{-- Daily activity chart with axes --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6 {{ $period === '7d' ? 'md:w-1/2' : '' }}">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Atividade Diária</h3>
            @php 
                $maxCount = max($dailyActivity->max('count'), 1);
                $ySteps = $maxCount <= 3 ? $maxCount : min(5, $maxCount);
                $yTickInterval = $maxCount <= 1 ? 1 : max(1, (int) ceil($maxCount / $ySteps));
                $yMax = $yTickInterval * $ySteps;
            @endphp
            <div class="flex gap-1">
                {{-- Y axis --}}
                <div class="flex flex-col justify-between pr-2 pt-1" style="height: 160px; min-width: 28px;">
                    @for($i = $ySteps; $i >= 0; $i--)
                        <span class="text-[10px] text-slate-400 text-right leading-none">{{ $i * $yTickInterval }}</span>
                    @endfor
                </div>

                {{-- Chart area --}}
                <div class="flex-1">
                    <div class="relative flex items-end gap-1" style="height: 160px;">
                        {{-- Horizontal grid lines --}}
                        @for($i = 0; $i <= $ySteps; $i++)
                            <div class="absolute left-0 right-0 border-t border-slate-100"
                                 style="bottom: {{ ($i * $yTickInterval / $yMax) * 100 }}%;"></div>
                        @endfor

                        {{-- Bars --}}
                        @foreach($dailyActivity as $day)
                            @php
                                $heightPercent = ($day['count'] / $yMax) * 100;
                                $barColor = $day['count'] > 0 
                                    ? 'linear-gradient(to top, #4f46e5, #818cf8)' 
                                    : '#e2e8f0';
                            @endphp
                            <div class="flex-1 flex flex-col items-center justify-end h-full min-w-0 relative z-10">
                                @if($day['count'] > 0)
                                    <span class="text-[10px] text-slate-500 font-medium mb-0.5">{{ $day['count'] }}</span>
                                @endif
                                <div class="w-full rounded-t-sm transition-all duration-300"
                                     style="height: {{ $heightPercent }}%;
                                            background: {{ $barColor }};
                                            min-height: {{ $day['count'] > 0 ? '4px' : '2px' }};">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- X axis labels --}}
                    <div class="flex gap-1 mt-2">
                        @foreach($dailyActivity as $day)
                            <div class="flex-1 min-w-0 text-center">
                                <span class="text-[10px] text-slate-400 hidden sm:block leading-tight">{{ $day['weekday'] }}</span>
                                <span class="text-[9px] text-slate-300 sm:hidden leading-tight">{{ substr($day['weekday'], 0, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between mt-0.5">
                        <span class="text-[10px] text-slate-400">{{ $dailyActivity->first()['day'] }}</span>
                        <span class="text-[10px] text-slate-400">{{ $dailyActivity->last()['day'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Per-subject report --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-700">Desempenho por Disciplina</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50">
                            <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">Disciplina</th>
                            <th class="text-center px-3 py-3 text-xs font-medium text-slate-500 uppercase">Tópicos</th>
                            <th class="text-center px-3 py-3 text-xs font-medium text-slate-500 uppercase">Revisões</th>
                            <th class="text-center px-3 py-3 text-xs font-medium text-slate-500 uppercase">Pendentes</th>
                            <th class="text-center px-3 py-3 text-xs font-medium text-slate-500 uppercase">Concluído</th>
                            <th class="text-center px-3 py-3 text-xs font-medium text-slate-500 uppercase">Acertos</th>
                            <th class="text-right px-5 py-3 text-xs font-medium text-slate-500 uppercase">Últ. Revisão</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($subjectReports as $report)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                                              style="background-color: {{ $report['subject']->color_code }}"></span>
                                        <span class="font-medium text-slate-800 truncate max-w-[180px]">
                                            {{ $report['subject']->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center px-3 py-3 text-slate-600">{{ $report['totalTopics'] }}</td>
                                <td class="text-center px-3 py-3 text-slate-600">
                                    <span class="font-medium">{{ $report['reviewsInPeriod'] }}</span>
                                    @if($report['reviewsAllTime'] !== $report['reviewsInPeriod'])
                                        <span class="text-xs text-slate-400">/{{ $report['reviewsAllTime'] }}</span>
                                    @endif
                                </td>
                                <td class="text-center px-3 py-3">
                                    @if($report['pending'] > 0)
                                        <span class="text-amber-600 font-medium">{{ $report['pending'] }}</span>
                                    @else
                                        <span class="text-emerald-500">✓</span>
                                    @endif
                                </td>
                                <td class="text-center px-3 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-16 h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-500"
                                                 style="width: {{ $report['completionPercent'] }}%;
                                                        background: {{ $report['completionPercent'] === 100 ? '#10b981' : 'linear-gradient(to right, #4f46e5, #6366f1)' }}">
                                            </div>
                                        </div>
                                        <span class="text-xs text-slate-500 w-8">{{ $report['completionPercent'] }}%</span>
                                    </div>
                                </td>
                                <td class="text-center px-3 py-3">
                                    @if($report['accuracy'] !== null)
                                        <span class="font-medium {{ $report['accuracy'] >= 70 ? 'text-emerald-600' : ($report['accuracy'] >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                                            {{ $report['accuracy'] }}%
                                        </span>
                                        <span class="text-[10px] text-slate-400 block">
                                            {{ $report['questionsCorrect'] }}/{{ $report['questionsTotal'] }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="text-right px-5 py-3 text-slate-500 text-xs">
                                    {{ $report['lastReview'] ? $report['lastReview']->format('d/m/Y') : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent activity --}}
        @if($recentActivity->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-700">Atividade Recente</h3>
                </div>
                <div class="divide-y divide-slate-50 max-h-80 overflow-y-auto">
                    @foreach($recentActivity as $activity)
                        <div class="flex items-center gap-3 px-5 py-3 hover:bg-slate-50 transition-colors">
                            <span class="w-2 h-2 rounded-full flex-shrink-0"
                                  style="background-color: {{ $activity->topic->subject->color_code }}"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-slate-700 truncate">{{ $activity->topic->title }}</p>
                                <p class="text-xs text-slate-400">{{ $activity->topic->subject->name }}</p>
                            </div>
                            <span class="text-xs text-slate-400 whitespace-nowrap">
                                {{ $activity->last_studied_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
