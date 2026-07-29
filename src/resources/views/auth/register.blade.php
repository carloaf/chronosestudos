<x-guest-layout>
    <!-- Brand / Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-5" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.4);">
            <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
            </svg>
        </div>
        <h1 style="font-size: 22px; font-weight: 700; color: #333; margin: 0 0 4px 0;">Criar Conta</h1>
        <p style="font-size: 12px; color: #999; margin: 0;">ChronosEstudos &mdash; Comece a organizar seus estudos</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nome -->
        <div class="mb-5">
            <label for="name" style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">Nome</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Seu nome completo"
                style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 15px; font-family: inherit; color: #333; background: #f8fafc; outline: none; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box;"
                onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'; this.style.background='#fff';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc';"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <!-- Email -->
        <div class="mb-5">
            <label for="email" style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
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
                autocomplete="new-password"
                placeholder="Mínimo 8 caracteres"
                style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 15px; font-family: inherit; color: #333; background: #f8fafc; outline: none; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box;"
                onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'; this.style.background='#fff';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc';"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Confirmar Senha -->
        <div class="mb-5">
            <label for="password_confirmation" style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">Confirmar Senha</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Repita a senha"
                style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 15px; font-family: inherit; color: #333; background: #f8fafc; outline: none; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box;"
                onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'; this.style.background='#fff';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc';"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <!-- Botão Cadastrar -->
        <button type="submit"
            style="width: 100%; padding: 14px; margin-top: 8px; border: none; border-radius: 25px; font-size: 15px; font-weight: 700; color: #fff; cursor: pointer; transition: all 0.25s ease; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.35);"
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(99,102,241,0.45)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(99,102,241,0.35)';"
        >
            Criar conta
        </button>

        <!-- Link Login -->
        <p style="text-align: center; margin-top: 20px; margin-bottom: 0; font-size: 13px; color: #888;">
            Já tem conta?
            <a href="{{ route('login') }}" style="color: #6366f1; text-decoration: none; font-weight: 600; margin-left: 4px;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                Fazer login
            </a>
        </p>
    </form>
</x-guest-layout>
