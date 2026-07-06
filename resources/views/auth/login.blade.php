<x-guest-layout>
    <div class="auth-header">
        <h2 class="auth-title">¡Bienvenido de nuevo!</h2>
        <p class="auth-subtitle">Ingresa tus credenciales para acceder</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div style="margin-bottom: 20px;">
            <label for="email">Correo Electrónico</label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="ejemplo@correo.com">
            <x-input-error :messages="$errors->get('email')" style="color: #ef4444; font-size: 0.8rem; margin-top: 5px;" />
        </div>

        <!-- Password -->
        <div style="margin-bottom: 20px;">
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" style="color: #ef4444; font-size: 0.8rem; margin-top: 5px;" />
        </div>

        <!-- Remember Me -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <label style="display: flex; align-items: center; cursor: pointer; text-transform: none; margin-left: 0;">
                <input id="remember_me" type="checkbox" name="remember" style="width: 18px; height: 18px; margin-right: 10px;">
                <span style="font-size: 0.85rem; color: #64748b;">Recordarme</span>
            </label>
            @if (Route::has('password.request'))
                <a style="font-size: 0.85rem; color: var(--primary); text-decoration: none; font-weight: 600;" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <button type="submit" class="btn-auth">
            Iniciar Sesión
        </button>

        <div style="text-align: center; margin-top: 25px; font-size: 0.9rem; color: #64748b;">
            ¿No tienes cuenta? 
            <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">Regístrate ahora</a>
        </div>
    </form>
</x-guest-layout>
