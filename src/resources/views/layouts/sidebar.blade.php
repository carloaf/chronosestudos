{{-- Sidebar Desktop --}}
<aside class="hidden lg:flex lg:flex-col fixed inset-y-0 left-0 z-40 bg-white border-r border-slate-200"
       style="width: var(--sidebar-w, 13rem)"
       x-bind:style="sidebarCollapsed ? 'width: 4rem' : 'width: 13rem'">

    {{-- Toggle button --}}
    <button @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebarCollapsed', sidebarCollapsed)"
            class="absolute -right-3 top-20 w-6 h-6 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-400 hover:text-indigo-600 shadow-sm transition-colors z-50">
        <svg class="w-3 h-3 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    {{-- Logo --}}
    <div class="flex items-center h-16 px-3 border-b border-slate-200 overflow-hidden">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <span class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <span x-show="!sidebarCollapsed" x-cloak class="text-lg font-bold text-slate-800 whitespace-nowrap">Chronos</span>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-2 py-6 space-y-1 overflow-y-auto overflow-x-hidden">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                  {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
           :class="sidebarCollapsed ? 'justify-center' : ''"
           :title="sidebarCollapsed ? 'Dashboard' : ''">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span x-show="!sidebarCollapsed" x-cloak class="whitespace-nowrap">Dashboard</span>
        </a>

        {{-- Meta do Dia --}}
        <a href="{{ route('daily-goal') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                  {{ request()->routeIs('daily-goal') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
           :class="sidebarCollapsed ? 'justify-center' : ''"
           :title="sidebarCollapsed ? 'Meta do Dia' : ''">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5h6m-6 4h.01M12 9h.01M15 9h.01M9 13h.01M12 13h.01M15 13h.01M9 17h.01M12 17h.01M15 17h.01"/></svg>
            <span x-show="!sidebarCollapsed" x-cloak class="whitespace-nowrap">Meta do Dia</span>
        </a>

        {{-- Disciplinas --}}
        <a href="{{ route('subjects.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                  {{ request()->routeIs('subjects.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
           :class="sidebarCollapsed ? 'justify-center' : ''"
           :title="sidebarCollapsed ? 'Disciplinas' : ''">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span x-show="!sidebarCollapsed" x-cloak class="whitespace-nowrap">Disciplinas</span>
        </a>

        {{-- Relatório --}}
        <a href="{{ route('reports.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                  {{ request()->routeIs('reports.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
           :class="sidebarCollapsed ? 'justify-center' : ''"
           :title="sidebarCollapsed ? 'Relatório' : ''">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span x-show="!sidebarCollapsed" x-cloak class="whitespace-nowrap">Relatório</span>
        </a>
    </nav>

    {{-- User area (bottom) --}}
    <div class="p-3 border-t border-slate-200 overflow-hidden">
        <div class="flex items-center gap-3" :class="sidebarCollapsed ? 'justify-center' : 'px-3'">
            <div class="w-8 h-8 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div x-show="!sidebarCollapsed" x-cloak class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-700 truncate">{{ Auth::user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-xs text-slate-400 hover:text-red-500 transition-colors">Sair</button>
                </form>
            </div>
        </div>
    </div>
</aside>
