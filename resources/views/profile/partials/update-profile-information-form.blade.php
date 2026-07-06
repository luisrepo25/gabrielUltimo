<section>
    <h3 style="margin-bottom: 6px;">Información Personal</h3>
    <p class="muted" style="font-size: 0.9rem; margin-bottom: 24px;">Actualiza tu nombre, correo y datos de contacto.</p>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <label for="nombre" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem;">Nombre</label>
                <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $user->nombre) }}"
                    required autofocus
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; background: var(--bg-light);">
                @error('nombre') <span style="color: var(--danger); font-size: 0.82rem;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="apellido" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem;">Apellido</label>
                <input id="apellido" name="apellido" type="text" value="{{ old('apellido', $user->apellido) }}"
                    required
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; background: var(--bg-light);">
                @error('apellido') <span style="color: var(--danger); font-size: 0.82rem;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <label for="email" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem;">Correo electrónico</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                required autocomplete="email"
                style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; background: var(--bg-light);">
            @error('email') <span style="color: var(--danger); font-size: 0.82rem;">{{ $message }}</span> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <label for="telefono" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem;">Teléfono</label>
                <input id="telefono" name="telefono" type="text" value="{{ old('telefono', $user->telefono) }}"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; background: var(--bg-light);">
                @error('telefono') <span style="color: var(--danger); font-size: 0.82rem;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="sexo" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem;">Sexo</label>
                <select id="sexo" name="sexo"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; background: var(--bg-light);">
                    <option value="M" {{ old('sexo', $user->sexo) === 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('sexo', $user->sexo) === 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
                @error('sexo') <span style="color: var(--danger); font-size: 0.82rem;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label for="domicilio" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem;">Domicilio</label>
            <input id="domicilio" name="domicilio" type="text" value="{{ old('domicilio', $user->domicilio) }}"
                style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; background: var(--bg-light);">
            @error('domicilio') <span style="color: var(--danger); font-size: 0.82rem;">{{ $message }}</span> @enderror
        </div>

        <div style="display: flex; align-items: center; gap: 16px;">
            <button type="submit" class="btn-save">Guardar cambios</button>
            @if (session('status') === 'profile-updated')
                <span style="color: var(--success); font-size: 0.9rem; font-weight: 600;">Guardado correctamente.</span>
            @endif
        </div>
    </form>
</section>
