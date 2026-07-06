<x-guest-layout>
    <div class="auth-header">
        <h2 class="auth-title">¿Olvidaste tu contraseña?</h2>
        <p class="auth-subtitle">Ingresa tu correo y te enviaremos un enlace para restablecerla</p>
    </div>

    <!-- Mensaje de éxito -->
    @if (session('status'))
        <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span style="color: #15803d; font-size: 0.9rem; font-weight: 600;">{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div style="margin-bottom: 24px;">
            <label for="email">Correo Electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="ejemplo@correo.com">
            <x-input-error :messages="$errors->get('email')" style="color: #ef4444; font-size: 0.8rem; margin-top: 5px;" />
        </div>

        <button type="submit" class="btn-auth">
            Enviar enlace de recuperación
        </button>

        <div style="text-align: center; margin-top: 22px; font-size: 0.9rem; color: #64748b;">
            <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">
                ← Volver al inicio de sesión
            </a>
        </div>
    </form>
</x-guest-layout>
