{{-- Bottom Nav Mobile --}}
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-slate-200 safe-area-bottom">
    <div class="flex items-center justify-around h-16">
        <a href="{{ route('dashboard') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors
                  {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] font-medium">Início</span>
        </a>

        <a href="{{ route('daily-goal') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors
                  {{ request()->routeIs('daily-goal') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5h6m-6 4h.01M12 9h.01M15 9h.01M9 13h.01M12 13h.01M15 13h.01M9 17h.01M12 17h.01M15 17h.01"/></svg>
            <span class="text-[10px] font-medium">Meta</span>
        </a>

        <a href="{{ route('subjects.index') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors
                  {{ request()->routeIs('subjects.*') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span class="text-[10px] font-medium">Disciplinas</span>
        </a>

        <a href="{{ route('reports.index') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors
                  {{ request()->routeIs('reports.*') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="text-[10px] font-medium">Relatório</span>
        </a>

        @if (Auth::user()->isAdmin())
            <a href="{{ route('admin.users.index') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors
                      {{ request()->routeIs('admin.*') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="text-[10px] font-medium">Admin</span>
            </a>
        @endif

        <a href="{{ route('profile.edit') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors
                  {{ request()->routeIs('profile.*') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[10px] font-medium">Perfil</span>
        </a>
    </div>
</nav>
