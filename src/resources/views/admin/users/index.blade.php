<x-app-layout>
    <x-slot name="header">
        Administração de Usuários
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm">
            {{ session('status') }}
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 flex flex-col sm:flex-row gap-3 sm:items-center">
        <div class="flex-1">
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nome ou e-mail..."
                   class="w-full px-4 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            @foreach ([
                'all' => 'Todos',
                'active' => 'Ativos',
                'blocked' => 'Bloqueados',
                'admin' => 'Admins',
            ] as $val => $label)
                <a href="{{ route('admin.users.index', ['status' => $val, 'q' => $q]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors
                          {{ $status === $val ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    {{ $label }}
                </a>
            @endforeach
            <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-medium hover:bg-slate-900">
                Buscar
            </button>
        </div>
    </form>

    {{-- Users table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden"
         x-data="{ editing: null, form: { name: '', email: '' } }">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">Nome</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">E-mail</th>
                        <th class="text-center px-3 py-3 text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="text-center px-3 py-3 text-xs font-medium text-slate-500 uppercase">Papel</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-slate-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($users as $u)
                        @php
                            $protected = $u->isProtectedAdmin();
                            $isSelf = auth()->id() === $u->id;
                            $isAdminRow = $u->isAdmin();
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.users.show', $u) }}" class="font-medium text-slate-800 hover:text-indigo-600">
                                    {{ $u->name }}
                                </a>
                                @if ($isSelf)
                                    <span class="ml-1 text-[10px] uppercase text-slate-400">(você)</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $u->email }}</td>
                            <td class="text-center px-3 py-3">
                                @if ($u->isActive())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">Ativo</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700">Bloqueado</span>
                                @endif
                            </td>
                            <td class="text-center px-3 py-3">
                                @if ($protected)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-indigo-100 text-indigo-800">Admin Fixo</span>
                                @elseif ($isAdminRow)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-indigo-50 text-indigo-700">Admin</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600">Comum</span>
                                @endif
                            </td>
                            <td class="text-right px-5 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.show', $u) }}"
                                       class="px-2 py-1 rounded-lg text-xs text-slate-600 hover:bg-slate-100" title="Ver">
                                        Ver
                                    </a>

                                    @unless ($protected)
                                        <button type="button"
                                                @click="editing = {{ $u->id }}; form.name = @js($u->name); form.email = @js($u->email)"
                                                class="px-2 py-1 rounded-lg text-xs text-indigo-600 hover:bg-indigo-50">
                                            Editar
                                        </button>
                                    @endunless

                                    @unless ($protected || $isSelf)
                                        <form method="POST" action="{{ route('admin.users.toggle-active', $u) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="px-2 py-1 rounded-lg text-xs {{ $u->isActive() ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}">
                                                {{ $u->isActive() ? 'Bloquear' : 'Ativar' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.users.toggle-admin', $u) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="px-2 py-1 rounded-lg text-xs text-indigo-600 hover:bg-indigo-50">
                                                {{ $u->is_admin ? 'Rebaixar' : 'Promover' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline"
                                              onsubmit="return confirm('Excluir este usuário? Esta ação é irreversível e apagará também suas disciplinas, tópicos e revisões.');">
                                            @csrf @method('DELETE')
                                            <button class="px-2 py-1 rounded-lg text-xs text-red-600 hover:bg-red-50">
                                                Excluir
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>

                        {{-- Edit modal (inline) --}}
                        @unless ($protected)
                            <tr x-show="editing === {{ $u->id }}" x-cloak>
                                <td colspan="5" class="bg-slate-50 px-5 py-4 border-t border-slate-100">
                                    <form method="POST" action="{{ route('admin.users.update', $u) }}"
                                          class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                                        @csrf @method('PATCH')
                                        <div class="flex-1 w-full">
                                            <label class="block text-xs text-slate-500 mb-1">Nome</label>
                                            <input type="text" name="name" x-model="form.name" required
                                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" />
                                        </div>
                                        <div class="flex-1 w-full">
                                            <label class="block text-xs text-slate-500 mb-1">E-mail</label>
                                            <input type="email" name="email" x-model="form.email" required
                                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" />
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit"
                                                    class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-xs font-medium hover:bg-indigo-700">
                                                Salvar
                                            </button>
                                            <button type="button" @click="editing = null"
                                                    class="px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-medium hover:bg-slate-50">
                                                Cancelar
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endunless
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-sm">
                                Nenhum usuário encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
