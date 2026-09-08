<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.index') }}" class="text-slate-400 hover:text-slate-600 text-sm">← Usuários</a>
            <span class="text-slate-300">/</span>
            <span>{{ $user->name }}</span>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm">
            {{ session('status') }}
        </div>
    @endif

    @php
        $protected = $user->isProtectedAdmin();
        $isSelf = auth()->id() === $user->id;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Info card --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xl font-semibold flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-slate-800">{{ $user->name }}</h2>
                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @if ($user->isActive())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">Ativo</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700">Bloqueado</span>
                        @endif

                        @if ($protected)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-indigo-100 text-indigo-800">Admin Fixo</span>
                        @elseif ($user->is_admin)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-indigo-50 text-indigo-700">Admin</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600">Comum</span>
                        @endif

                        @if ($isSelf)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500">Você</span>
                        @endif
                    </div>
                    <p class="mt-3 text-xs text-slate-400">
                        Cadastrado em {{ $user->created_at?->format('d/m/Y') ?? '—' }}
                    </p>
                </div>
            </div>

            @unless ($protected)
                <hr class="my-6 border-slate-100" />

                <h3 class="text-sm font-semibold text-slate-700 mb-3">Editar dados</h3>
                <form method="POST" action="{{ route('admin.users.update', $user) }}"
                      class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Nome</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" />
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">E-mail</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" />
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2 flex justify-end">
                        <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                            Salvar alterações
                        </button>
                    </div>
                </form>
            @endunless
        </div>

        {{-- Actions + summary --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Resumo de Estudos</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Disciplinas</dt>
                        <dd class="font-semibold text-slate-800">{{ $summary['subjects'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Tópicos</dt>
                        <dd class="font-semibold text-slate-800">{{ $summary['topics'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Recursos</dt>
                        <dd class="font-semibold text-slate-800">{{ $summary['resources'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Revisões pendentes</dt>
                        <dd class="font-semibold text-amber-600">{{ $summary['pendingReviews'] }}</dd>
                    </div>
                </dl>
            </div>

            @unless ($protected)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-3">
                    <h3 class="text-sm font-semibold text-slate-700">Ações</h3>

                    @unless ($isSelf)
                        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                            @csrf @method('PATCH')
                            <button class="w-full px-3 py-2 rounded-lg text-sm font-medium
                                           {{ $user->isActive() ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                {{ $user->isActive() ? 'Bloquear usuário' : 'Ativar usuário' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}">
                            @csrf @method('PATCH')
                            <button class="w-full px-3 py-2 rounded-lg text-sm font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                                {{ $user->is_admin ? 'Rebaixar de administrador' : 'Promover a administrador' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('Excluir este usuário? Esta ação é irreversível.');">
                            @csrf @method('DELETE')
                            <button class="w-full px-3 py-2 rounded-lg text-sm font-medium bg-red-50 text-red-700 hover:bg-red-100">
                                Excluir usuário
                            </button>
                        </form>
                    @else
                        <p class="text-xs text-slate-400">Você não pode alterar seu próprio status por aqui.</p>
                    @endunless
                </div>
            @else
                <div class="bg-slate-50 rounded-xl border border-slate-200 p-6 text-sm text-slate-500">
                    Este é um <strong>administrador fixo</strong> do sistema e não pode ser editado, bloqueado, rebaixado ou excluído.
                </div>
            @endunless
        </div>
    </div>
</x-app-layout>
