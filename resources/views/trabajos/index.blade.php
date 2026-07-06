@extends('layouts.ferreteria')

@section('title', 'Roles y Trabajos - Ferretería Guisella')

@section('content')
<style>
    /* ── Responsive trabajos ── */
    .trabajos-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 30px;
        align-items: start;
    }
    @media (max-width: 900px) {
        .trabajos-grid {
            grid-template-columns: 1fr;
        }
    }

    .baja-btn {
        background: none;
        border: 1px solid var(--danger);
        color: var(--danger);
        padding: 4px 12px;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
        white-space: nowrap;
    }
    .baja-btn:hover {
        background: var(--danger);
        color: #fff;
    }

    /* Celdas del grid no se expanden más allá del viewport */
    .trabajos-grid > * {
        min-width: 0;
        max-width: 100%;
    }

    /* Tabla responsive: scroll en móvil */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        max-width: 100%;
        display: block;
    }
    .table-responsive table { min-width: 520px; }

    /* Columna acciones no colapsa */
    .col-acciones { width: 110px; text-align: center; }

    /* Formularios del panel lateral */
    .trabajos-forms select,
    .trabajos-forms input[type="text"],
    .trabajos-forms button {
        width: 100%;
        box-sizing: border-box;
    }

    @media (max-width: 900px) {
        .trabajos-forms {
            width: 100%;
            max-width: 100%;
        }
    }

    @media (max-width: 600px) {
        .page-header h1 { font-size: 1.3rem; }
        .card { padding: 16px; }
        .trabajos-forms { gap: 16px; }
        .baja-btn { font-size: 0.75rem; padding: 4px 8px; }
        .col-fecha-inicio { display: none; }
    }

    @media (max-width: 480px) {
        .trabajos-grid { gap: 20px; }
        .table-responsive th,
        .table-responsive td { padding: 10px 8px; }
        .col-acciones { width: auto; }
    }
</style>

<div class="animate-fade-up">
    <div class="page-header">
        <div>
            <h1 style="margin: 0;">Centro de Asignaciones</h1>
            <p class="subtitle" style="margin: 0;">Gestiona y revisa los trabajos y roles del personal.</p>
        </div>
    </div>

    @if(session('success_rol'))
        <div class="alert alert-success"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> {{ session('success_rol') }}</div>
    @endif
    @if(session('success_asignacion'))
        <div class="alert alert-success"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> {{ session('success_asignacion') }}</div>
    @endif
    @if(session('success_baja'))
        <div class="alert alert-success"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> {{ session('success_baja') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> Por favor revisa los datos ingresados e intenta otra vez.</div>
    @endif

    <div class="trabajos-grid">

        {{-- ── Tabla de Asignaciones ── --}}
        <div class="card" style="margin-bottom: 0;">
            <div style="margin-bottom: 24px;">
                <h2 style="margin: 0;">Tabla de Asignaciones</h2>
                <p class="muted" style="font-size:0.9rem; margin: 4px 0 0 0;">
                    @if($isAdmin)
                        Visualizando todas las asignaciones de la empresa.
                    @else
                        Visualizando únicamente los trabajos asignados para ti.
                    @endif
                </p>
            </div>

            <div class="table-responsive">
                @if($asignaciones->count() > 0)
                    <table style="min-width: 500px;">
                        <thead>
                            <tr>
                                <th>Rol / Trabajo</th>
                                @if($isAdmin)<th>Empleado</th>@endif
                                <th class="col-fecha-inicio">Fecha Inicio</th>
                                <th>Estado</th>
                                @if($isAdmin)<th class="col-acciones">Acciones</th>@endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($asignaciones as $asignacion)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-main);">{{ $asignacion->rol->nombre ?? 'N/A' }}</div>
                                    <div style="font-size: 0.8rem;" class="muted">{{ $asignacion->rol->descripcion ?? '' }}</div>
                                </td>
                                @if($isAdmin)
                                <td>
                                    <div style="font-weight: 600;">{{ $asignacion->empleado->usuario->nombre ?? 'Desconocido' }} {{ $asignacion->empleado->usuario->apellido ?? '' }}</div>
                                    <div style="font-size: 0.8rem;" class="muted">CI: {{ $asignacion->ci_empleado }}</div>
                                </td>
                                @endif
                                <td class="col-fecha-inicio" style="white-space: nowrap;">{{ $asignacion->fechaInicio }}</td>
                                <td>
                                    <span class="badge {{ strtolower($asignacion->estado) == 'activo' ? 'activo' : '' }}">
                                        {{ $asignacion->estado }}
                                    </span>
                                </td>
                                @if($isAdmin)
                                <td class="col-acciones">
                                    @if(strtolower($asignacion->estado) === 'activo')
                                        <form method="POST" action="{{ route('trabajos.baja') }}"
                                              onsubmit="return confirm('¿Dar de baja esta asignación? Si el empleado queda sin roles activos pasará a ser Cliente.')">
                                            @csrf
                                            <input type="hidden" name="id_rol"      value="{{ $asignacion->id_rol }}">
                                            <input type="hidden" name="ci_empleado" value="{{ $asignacion->ci_empleado }}">
                                            <button type="submit" class="baja-btn">Dar de baja</button>
                                        </form>
                                    @else
                                        <span style="font-size:0.8rem; color:var(--text-muted);">—</span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="padding:40px; text-align:center; background:#f8fafc; border-radius:var(--radius-sm); border:1px dashed var(--border);">
                        <p style="color:var(--text-muted); font-weight:500; font-size:1.1rem;">No hay asignaciones registradas.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Panel lateral admin ── --}}
        @can('admin')
        <div style="display:flex; flex-direction:column; gap:24px; min-width:0; max-width:100%; width:100%;" class="trabajos-forms">

            <div class="card" style="margin-bottom: 0;">
                <h2 style="display: flex; align-items: center; gap: 8px; font-size: 1.1rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>
                    Asignar Trabajo
                </h2>
                <form action="{{ route('trabajos.asignar') }}" method="POST">
                    @csrf
                    <div class="field" style="margin-bottom: 14px;">
                        <label>Empleado</label>
                        <select name="ci_empleado" required>
                            <option value="">— Elegir Empleado —</option>
                            @foreach($empleados as $emp)
                                <option value="{{ $emp->ci }}">{{ $emp->nombre }} {{ $emp->apellido }} (CI: {{ $emp->ci }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field" style="margin-bottom: 20px;">
                        <label>Rol / Trabajo</label>
                        <select name="id_rol" required>
                            <option value="">— Elegir Trabajo —</option>
                            @foreach($rolesDisponibles as $r)
                                <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-save" style="width: 100%;">Guardar Asignación</button>
                </form>
            </div>

            <div class="card" style="background:var(--bg-light); border:1px dashed var(--border); box-shadow:none; margin-bottom: 0;">
                <h2 style="font-size: 1.1rem;">Registrar Nuevo Rol</h2>
                <form action="{{ route('trabajos.store') }}" method="POST">
                    @csrf
                    <div class="field" style="margin-bottom: 14px;">
                        <label>Nombre del Trabajo</label>
                        <input type="text" name="nombre" placeholder="Ej: Gerente, Vendedor" required style="background: white; width: 100%; box-sizing: border-box;">
                    </div>
                    <div class="field" style="margin-bottom: 20px;">
                        <label>Descripción</label>
                        <input type="text" name="descripcion" placeholder="Breve descripción" style="background: white; width: 100%; box-sizing: border-box;">
                    </div>
                    <button type="submit" class="btn-action" style="width: 100%;">Crear Rol en Sistema</button>
                </form>
            </div>

        </div>
        @endcan

    </div>
</div>
@endsection
