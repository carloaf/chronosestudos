<x-guest-layout>
    <!-- Brand / Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-5" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.4);">
            <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-800 mb-1">Acessar Sistema</h1>
        <p class="text-xs text-gray-400">ChronosEstudos &mdash; Cronograma com Repetição Espaçada</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-5">
            <label for="email" style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="seu@email.com"
                style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 15px; font-family: inherit; color: #333; background: #f8fafc; outline: none; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box;"
                onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'; this.style.background='#fff';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc';"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Senha -->
        <div class="mb-5">
            <label for="password" style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">Senha</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 15px; font-family: inherit; color: #333; background: #f8fafc; outline: none; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box;"
                onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'; this.style.background='#fff';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc';"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Lembrar-me + Esqueceu senha -->
        <div class="flex items-center justify-between mb-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                <input id="remember_me" type="checkbox" name="remember" style="accent-color: #6366f1; width: 16px; height: 16px; margin-right: 8px; cursor: pointer;" />
                <span style="font-size: 13px; color: #666;">Lembrar-me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="font-size: 13px; color: #6366f1; text-decoration: none; font-weight: 500;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                    Esqueceu a senha?
                </a>
            @endif
        </div>

        <!-- Botão Entrar -->
        <button type="submit"
            style="width: 100%; padding: 14px; border: none; border-radius: 25px; font-size: 15px; font-weight: 700; color: #fff; cursor: pointer; transition: all 0.25s ease; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.35);"
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(99,102,241,0.45)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(99,102,241,0.35)';"
        >
            Entrar
        </button>

        <!-- Link Registrar -->
        <p style="text-align: center; margin-top: 20px; margin-bottom: 0; font-size: 13px; color: #888;">
            Não tem conta?
            <a href="{{ route('register') }}" style="color: #6366f1; text-decoration: none; font-weight: 600; margin-left: 4px;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                Cadastre-se
            </a>
        </p>
    </form>
</x-guest-layout>
