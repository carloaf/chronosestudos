<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-5" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.4);">
            <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            </svg>
        </div>
        <h1 style="font-size: 22px; font-weight: 700; color: #333; margin: 0 0 4px 0;">Verificar Email</h1>
        <p style="font-size: 12px; color: #999; margin: 0;">Confirme seu endereço de email para continuar</p>
    </div>

    <p style="font-size: 13px; color: #666; line-height: 2; margin-bottom: 20px;">
        Obrigado por se cadastrar! Antes de começar, verifique seu email clicando no link que enviamos. Se não recebeu, podemos reenviar.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; padding: 12px 16px; font-size: 13px; margin-bottom: 20px; line-height: 1.5;">
            Um novo link de verificação foi enviado para o email informado no cadastro.
        </div>
    @endif

    <div class="flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                style="padding: 12px 24px; border: none; border-radius: 25px; font-size: 14px; font-weight: 700; color: #fff; cursor: pointer; transition: all 0.25s ease; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.35); white-space: nowrap;"
                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(99,102,241,0.45)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(99,102,241,0.35)';">
                Reenviar Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="font-size: 13px; color: #888; background: none; border: none; cursor: pointer;" onmouseover="this.style.color='#6366f1'" onmouseout="this.style.color='#888'">
                Sair
            </button>
        </form>
    </div>
</x-guest-layout>
