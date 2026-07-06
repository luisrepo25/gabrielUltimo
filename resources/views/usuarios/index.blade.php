@extends('layouts.ferreteria')

@section('title', 'Gestión de Usuarios - Ferretería Guisella')

@section('content')
<div class="animate-fade-up">
    <div class="page-header">
        <div>
            <h1 style="margin: 0;">Gestión de Personal y Usuarios</h1>
            <p class="subtitle" style="margin: 0;">Directorio de recursos humanos, administradores y clientes.</p>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> {{ session('success') }}</div> @endif
    @if(session('success_eliminar')) <div class="alert alert-success"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> {{ session('success_eliminar') }}</div> @endif
    @if(session('error_general')) <div class="alert alert-error"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> {{ session('error_general') }}</div> @endif
    @if($errors->any()) <div class="alert alert-error"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> {{ $errors->first() }}</div> @endif

    @can('admin')
    <div class="action-buttons" style="margin-bottom: 30px;">
        <button class="btn-action" id="btn-toggle-estudio">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            Estudio Téc. (Ver Info)
        </button>
        <button class="btn-action" id="btn-toggle-modificar">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            Modificar Perfil
        </button>
        <button class="btn-action danger" id="btn-toggle-eliminar">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
            Borrar Acceso
        </button>
    </div>

    <!-- FORMULARIO ESTUDIO -->
    <div class="form-container d-none" id="container-estudio">
        <h3>Estudio Analítico de Usuario</h3>
        <p id="estudio-error-msg" class="error-text d-none" style="margin-bottom: 15px;"></p>
        <div class="form-grid">
            <div class="field" style="max-width: 400px;">
                <label>Carnet ID a estudiar:</label>
                <input type="text" id="ci-estudio" placeholder="Ingrese CI de la persona..." autocomplete="off">
            </div>
        </div>

        <div id="study-results" class="study-box d-none" style="margin-top: 24px; padding: 24px; background: #f8fafc; border-radius: var(--radius-md); border: 1px dashed var(--border);">
            <h4 style="margin: 0 0 12px 0; color:var(--primary);">Ficha Personal: <span id="study-name"></span></h4>
            <p style="margin: 8px 0; font-size: 0.95rem;"><strong>Correo Contacto:</strong> <span id="study-mail"></span></p>
            <p style="margin: 8px 0; font-size: 0.95rem;"><strong>Tipo Perfil:</strong> <span id="study-tipo"></span></p>
            <p style="margin: 8px 0; font-size: 0.95rem;"><strong>Categoría Cliente:</strong> <span id="study-categoria"></span></p>

            <h4 style="margin: 20px 0 12px 0; color:var(--text-main);">Historial de Roles / Asignaciones Activas:</h4>
            <ul class="task-list" id="study-tasks">
                <!-- Javascript rellenará aquí -->
            </ul>
        </div>
    </div>

    <!-- FORMULARIO MODIFICAR -->
    <div class="form-container d-none" id="container-modificar">
        <h3>Modificar Perfil Operativo</h3>

        <div class="form-grid" style="margin-bottom: 24px;">
            <div class="field" style="grid-column: 1 / -1; max-width: 400px;">
                <label>Buscar Carnet por Modificar:</label>
                <input type="text" id="ci-modificar" placeholder="ID de la persona a editar...">
                <span id="modificar-error-msg" class="error-text d-none"></span>
            </div>
        </div>
        
        <form id="form-modificar" action="#" method="POST" style="border-top: 1px dashed var(--border); padding-top: 24px;">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="field"><label>Nombre</label><input type="text" id="modnombre" name="nombre" required></div>
                <div class="field"><label>Apellido</label><input type="text" id="modapellido" name="apellido" required></div>
                <div class="field"><label>Teléfono</label><input type="text" id="modtelefono" name="telefono"></div>
                <div class="field">
                    <label>Sexo</label>
                    <select id="modsexo" name="sexo">
                        <option value="M">Masculino (M)</option>
                        <option value="F">Femenino (F)</option>
                    </select>
                </div>
                <div class="field"><label>Correo Electrónico (Auth)</label><input type="email" id="modcorreo" name="correo" required></div>
                <div class="field"><label>Domicilio</label><input type="text" id="moddomicilio" name="domicilio"></div>
                <div class="field"><label>Categoría Cliente</label><input type="text" id="modcategoria" name="categoria" placeholder="Ej: Hogar, Construcción"></div>
                <div class="field"><label>Tipo de Persona</label><input type="text" id="modtipo" name="tipoPersona" required></div>
            </div>
            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn-save">Actualizar Perfil</button>
            </div>
        </form>
    </div>

    <!-- FORMULARIO ELIMINAR -->
    <div class="form-container danger d-none" id="container-eliminar">
        <h3 style="display: flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            Eliminar usuario irrevocablemente
        </h3>
        <form id="form-eliminar" action="#" method="POST">
            @csrf
            @method('DELETE')
            <div class="form-grid">
                <div class="field" style="max-width: 300px;">
                    <label>Carnet de Identidad</label>
                    <input type="text" id="ci-eliminar" name="ci" placeholder="Digita el CI de la persona" required>
                    <span id="eliminar-error-msg" class="error-text d-none"></span>
                </div>
                <div class="field" style="max-width: 300px;">
                    <label>Usuario a eliminar</label>
                    <input type="text" id="delnombre" placeholder="Sujeto (Autocompletado)" readonly style="background: var(--bg-light); cursor:not-allowed;">
                </div>
            </div>
            <div style="margin-top: 20px;">
                <button type="submit" class="btn-logout" style="width: 100%; max-width: 300px;">Destruir Cuenta</button>
            </div>
        </form>
    </div>
    @endcan

    <div class="card">
        <h2 style="margin-bottom: 20px;">Directorio General</h2>
        <div class="table-wrap">
            @if($usuarios->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>CI (Identidad)</th>
                            <th>Nombre Completo</th>
                            <th>Teléfono</th>
                            <th>Correo Sincronizado</th>
                            <th>Rol Social</th>
                            <th>Categoría Cliente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $u)
                                <tr>
                            <td><span class="badge">{{ $u->ci }}</span></td>
                            <td><strong>{{ $u->nombre }} {{ $u->apellido }}</strong></td>
                            <td class="muted">{{ $u->telefono ?? 'S/R' }}</td>
                            <td>{{ $u->correo }}</td>
                            <td>
                                @if(strtoupper($u->tipoPersona) == 'ADMIN')
                                    <span class="badge bg-insert">{{ $u->tipoPersona }}</span>
                                @else
                                    <span class="badge">{{ $u->tipoPersona }}</span>
                                @endif
                            </td>
                            <td class="muted">{{ $u->cliente->categoria ?? ($u->tipoPersona === 'C' ? 'Sin categoría' : 'N/A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="padding:40px; text-align:center; background:#f8fafc; border-radius:var(--radius-sm); border:1px dashed var(--border);">
                    <p style="color:var(--text-muted); font-weight:500; font-size:1.1rem;">No hay base de usuarios instalada.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/ferreteria-usuarios.js') }}"></script>
@endpush
