<section>
    <h3 style="margin-bottom: 6px;">Cambiar Contraseña</h3>
    <p class="muted" style="font-size: 0.9rem; margin-bottom: 24px;">Usa una contraseña segura y única.</p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div style="margin-bottom: 16px;">
            <label for="update_password_current_password" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem;">Contraseña actual</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; background: var(--bg-light);">
            @error('current_password', 'updatePassword')
                <span style="color: var(--danger); font-size: 0.82rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 16px;">
            <label for="update_password_password" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem;">Nueva contraseña</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; background: var(--bg-light);">
            @error('password', 'updatePassword')
                <span style="color: var(--danger); font-size: 0.82rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 24px;">
            <label for="update_password_password_confirmation" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem;">Confirmar nueva contraseña</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; background: var(--bg-light);">
        </div>

        <div style="display: flex; align-items: center; gap: 16px;">
            <button type="submit" class="btn-save">Actualizar contraseña</button>
            @if (session('status') === 'password-updated')
                <span style="color: var(--success); font-size: 0.9rem; font-weight: 600;">Contraseña actualizada.</span>
            @endif
        </div>
    </form>
</section>
