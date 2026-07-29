<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-5" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.4);">
            <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
            </svg>
        </div>
        <h1 style="font-size: 22px; font-weight: 700; color: #333; margin: 0 0 4px 0;">Confirmar Senha</h1>
        <p style="font-size: 12px; color: #999; margin: 0;">Esta é uma área segura. Confirme sua senha para continuar.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-5">
            <label for="password" style="display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px;">Senha</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 15px; font-family: inherit; color: #333; background: #f8fafc; outline: none; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box;"
                onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'; this.style.background='#fff';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f8fafc';" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <button type="submit"
            style="width: 100%; padding: 14px; border: none; border-radius: 25px; font-size: 15px; font-weight: 700; color: #fff; cursor: pointer; transition: all 0.25s ease; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.35);"
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(99,102,241,0.45)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(99,102,241,0.35)';">
            Confirmar
        </button>
    </form>
</x-guest-layout>
