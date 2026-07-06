<x-guest-layout>
    <div class="auth-header">
        <h2 class="auth-title">Crea tu cuenta</h2>
        <p class="auth-subtitle">Únete a la familia Ferretería Guisella</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <!-- CI -->
            <div>
                <label for="ci">N° Cédula (CI)</label>
                <input id="ci" type="number" name="ci" :value="old('ci')" required placeholder="1234567">
                <x-input-error :messages="$errors->get('ci')" style="color: #ef4444; font-size: 0.75rem;" />
            </div>

            <!-- Sexo -->
            <div>
                <label for="sexo">Sexo</label>
                <select id="sexo" name="sexo" required>
                    <option value="">Elegir...</option>
                    <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
                <x-input-error :messages="$errors->get('sexo')" style="color: #ef4444; font-size: 0.75rem;" />
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <!-- Nombre -->
            <div>
                <label for="nombre">Nombre</label>
                <input id="nombre" type="text" name="nombre" :value="old('nombre')" required placeholder="Juan">
                <x-input-error :messages="$errors->get('nombre')" />
            </div>

            <!-- Apellido -->
            <div>
                <label for="apellido">Apellido</label>
                <input id="apellido" type="text" name="apellido" :value="old('apellido')" required placeholder="Pérez">
                <x-input-error :messages="$errors->get('apellido')" />
            </div>
        </div>

        <!-- Email Address -->
        <div style="margin-bottom: 15px;">
            <label for="email">Correo Electrónico</label>
            <input id="email" type="email" name="email" :value="old('email')" required placeholder="juan@correo.com">
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
            <!-- Password -->
            <div>
                <label for="password">Contraseña</label>
                <input id="password" type="password" name="password" required placeholder="••••••••">
                <small style="color: var(--text-muted, #6b7280); font-size: 0.78rem; margin-top: 4px; display: block;">
                    Mínimo 8 caracteres con mayúsculas, minúsculas y números. Ej: <em>Texto12345</em>
                </small>
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation">Confirmar</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="btn-auth">
            Crear mi cuenta
        </button>

        <div style="text-align: center; margin-top: 25px; font-size: 0.9rem; color: #64748b;">
            ¿Ya tienes una cuenta? 
            <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">Inicia sesión</a>
        </div>
    </form>
</x-guest-layout>
