<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-5" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.4);">
            <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
            </svg>
        </div>
        <h1 style="font-size: 22px; font-weight: 700; color: #333; margin: 0 0 4px 0;">Redefinir Senha</h1>
        <p style="font-size: 12px; color: #999; margin: 0;">Escolha uma nova senha para sua conta</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-5">
            <label for="email" style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 15px; font-family: inherit; color: #333; background: #f8fafc; outline: none; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box;"
                onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'; this.style.background='#fff';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc';" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="mb-5">
            <label for="password" style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">Nova Senha</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres"
                style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 15px; font-family: inherit; color: #333; background: #f8fafc; outline: none; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box;"
                onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'; this.style.background='#fff';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc';" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="mb-5">
            <label for="password_confirmation" style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">Confirmar Senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repita a senha"
                style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 15px; font-family: inherit; color: #333; background: #f8fafc; outline: none; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box;"
                onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'; this.style.background='#fff';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc';" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <button type="submit"
            style="width: 100%; padding: 14px; border: none; border-radius: 25px; font-size: 15px; font-weight: 700; color: #fff; cursor: pointer; transition: all 0.25s ease; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.35);"
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(99,102,241,0.45)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(99,102,241,0.35)';">
            Redefinir Senha
        </button>
    </form>
</x-guest-layout>
