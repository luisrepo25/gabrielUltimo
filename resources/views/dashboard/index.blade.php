@extends('layouts.ferreteria')

@section('title', 'Panel de Control - Ferretería Guisella')

@section('content')
<style>
/* ── Dashboard responsive ── */
.admin-right-col {
    display: flex;
    flex-direction: column;
    gap: 24px;
    min-width: 0;
    width: 100%;
}

.quick-actions-card {
    background: var(--primary-gradient) !important;
    color: white;
    border: none !important;
    box-shadow: 0 8px 24px rgba(13,148,136,0.3) !important;
    width: 100%;
    box-sizing: border-box;
}
.quick-actions-card h3 { color: white; margin-bottom: 16px; }

.quick-action-btn {
    display: block;
    width: 100%;
    box-sizing: border-box;
    padding: 14px 16px;
    border-radius: var(--radius-sm);
    text-align: center;
    font-weight: 700;
    font-size: 0.95rem;
    text-decoration: none;
    transition: background 0.2s;
    white-space: normal;
    word-break: break-word;
}
.quick-action-btn.primary  { background: rgba(255,255,255,0.22); color: white; }
.quick-action-btn.secondary{ background: rgba(255,255,255,0.10); color: white; font-weight: 600; margin-top: 10px; }
.quick-action-btn:hover { background: rgba(255,255,255,0.35) !important; color: white !important; }

.support-card {
    background: var(--bg-light) !important;
    border: 1px dashed var(--border) !important;
    width: 100%;
    box-sizing: border-box;
}
.support-card p { line-height: 1.6; font-size: 0.9rem; }

.bitacora-time { white-space: nowrap; }

/* Forzar contenedor bitácora a respetar ancho */
.bitacora-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
    max-width: 100%;
    display: block;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--surface);
}
.bitacora-wrap table {
    min-width: 520px;
    width: 100%;
    border-collapse: collapse;
}

@media (max-width: 768px) {
    .dashboard-welcome h1   { font-size: 1.4rem; }
    .dashboard-welcome .subtitle { font-size: 0.95rem; }
    .stat-grid { grid-template-columns: 1fr !important; }
    .admin-right-col { gap: 16px; }
    .quick-actions-card,
    .support-card { padding: 20px !important; }
}
@media (max-width: 480px) {
    .dashboard-welcome h1 { font-size: 1.2rem; }
    .quick-action-btn { padding: 12px; font-size: 0.9rem; }
}
</style>
<div class="animate-fade-up">
    <div class="page-header">
        <div class="dashboard-welcome">
            <h1>Bienvenido, {{ Auth::user()->nombre }}</h1>
            <p class="subtitle" style="margin: 0;">
                @if($isAdmin)
                    Resumen del estado actual de la ferretería
                @else
                    Aquí están los detalles de tu cuenta
                @endif
            </p>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid var(--success); color: var(--success); padding: 16px; margin-bottom: 24px; border-radius: 4px; display: flex; align-items: center; gap: 10px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <strong style="font-size: 1rem;">¡Éxito!</strong> {{ session('success') }}
        </div>
    @endif

    @if($isAdmin)
        {{-- ═══════════════════════════════════════════════ --}}
        {{-- VISTA ADMINISTRADOR                             --}}
        {{-- ═══════════════════════════════════════════════ --}}

        {{-- Tarjetas de Estadísticas --}}
        
        @if(isset($finanzas))
        <div class="card" style="margin-bottom: 24px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 12px; margin-bottom: 16px;">
                <h3 style="margin: 0; display: flex; align-items: center; gap: 8px; color: #f8fafc;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Inteligencia Financiera (Tiempo Real)
                </h3>
                <span style="font-size: 0.85rem; color: #94a3b8; display: flex; align-items: center; gap: 4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.59-9.21l5.25 4.64"/></svg>
                    Fuente: {{ $finanzas['source'] }}
                </span>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div>
                    <span style="color: #94a3b8; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Ingresos (BOB)</span>
                    <div style="font-size: 2rem; font-weight: 700; color: #10b981; margin-top: 4px;">Bs. {{ number_format($finanzas['BOB'], 2) }}</div>
                </div>
                <div style="border-left: 1px solid rgba(255,255,255,0.1); padding-left: 20px;">
                    <span style="color: #94a3b8; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Conversión a Dólares (USD)</span>
                    <div style="font-size: 1.8rem; font-weight: 700; color: #38bdf8; margin-top: 4px;">$ {{ number_format($finanzas['USD'], 2) }}</div>
                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 4px;">Tasa actual: 1 USD = {{ number_format($finanzas['rate_usd_bob'], 2) }} BOB</div>
                </div>
                <div style="border-left: 1px solid rgba(255,255,255,0.1); padding-left: 20px;">
                    <span style="color: #94a3b8; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Conversión a Euros (EUR)</span>
                    <div style="font-size: 1.8rem; font-weight: 700; color: #f472b6; margin-top: 4px;">€ {{ number_format($finanzas['EUR'], 2) }}</div>
                </div>
            </div>
        </div>
        @endif

        <div class="stat-grid">

            <div class="stat-card">
                <div class="stat-icon" style="background: var(--success-light); color: var(--success);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                </div>
                <div class="stat-info">
                    <span>Total Productos</span>
                    <div>{{ $totalProductos }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="stat-info">
                    <span>Personal & Usuarios</span>
                    <div>{{ $totalUsuarios }}</div>
                </div>
            </div>

            <div class="stat-card" style="border-left: 4px solid var(--danger);">
                <div class="stat-icon" style="background: var(--danger-light); color: var(--danger);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                </div>
                <div class="stat-info">
                    <span style="color: var(--danger);">Alertas Predictivas</span>
                    <div style="color: var(--danger);">{{ count($predicciones) }}</div>
                </div>
            </div>

        </div>

        @if(count($predicciones) > 0)
        <div class="card" style="margin-bottom: 24px; border-left: 4px solid var(--danger);">
            <h3 style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; color: var(--danger);">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Predicciones de Agotamiento (Próximos 7 días)
            </h3>
            <div class="bitacora-wrap">
                <table class="bitacora-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Stock Actual</th>
                            <th>Velocidad (x día)</th>
                            <th>Días Restantes</th>
                            <th>Acción Sugerida</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($predicciones as $pred)
                        <tr>
                            <td style="font-weight: 600;">{{ $pred->nombre }}</td>
                            <td>{{ $pred->stock_actual }} u.</td>
                            <td>{{ number_format($pred->velocidad_diaria, 1) }} u/día</td>
                            <td><strong style="color: var(--danger);">{{ number_format($pred->dias_restantes, 1) }} Días</strong></td>
                            <td>
                                <form action="{{ route('dashboard.generar_pedido', $pred->idproducto) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <input type="hidden" name="velocidad" value="{{ $pred->velocidad_diaria }}">
                                    <button type="submit" class="btn btn-sm btn-primary" style="background: var(--danger); border: none; padding: 6px 12px; border-radius: 4px; color: white; cursor: pointer; font-size: 0.85rem; font-weight: bold;">+ Generar Pedido</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="dashboard-grid admin-grid">

            {{-- Últimas Actividades --}}
            <div class="card">
                <h3 style="display: flex; align-items: center; gap: 10px; margin-bottom: 24px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20v-6M9 20v-10M15 20v-4M3 20h18"/></svg>
                    Actividad Reciente en Bitácora
                </h3>
                <div class="bitacora-wrap">
                    <table class="bitacora-table">
                        <thead>
                            <tr>
                                <th>Acción</th>
                                <th>Detalle</th>
                                <th>Tiempo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasBitacoras as $log)
                            <tr>
                                <td>
                                    @if(strtoupper($log->accion) == 'INSERTAR')
                                        <span class="badge bg-insert">{{ $log->accion }}</span>
                                    @elseif(strtoupper($log->accion) == 'ACTUALIZAR')
                                        <span class="badge bg-update">{{ $log->accion }}</span>
                                    @elseif(strtoupper($log->accion) == 'ELIMINAR')
                                        <span class="badge bg-delete">{{ $log->accion }}</span>
                                    @else
                                        <span class="badge">{{ $log->accion }}</span>
                                    @endif
                                </td>
                                <td>{{ $log->descripcion }}</td>
                                <td class="muted bitacora-time">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-right" style="margin-top: 16px;">
                    <a href="{{ route('bitacora.index') }}">Ver bitácora completa →</a>
                </div>
            </div>

            {{-- Accesos Rápidos + Soporte --}}
            <div class="admin-right-col">
                <div class="card quick-actions-card">
                    <h3>Acciones Rápidas</h3>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <a href="{{ route('productos.create') }}" class="quick-action-btn primary">+ Nuevo Producto</a>
                        <a href="{{ route('usuarios.index') }}" class="quick-action-btn secondary">Gestionar Personal</a>
                    </div>
                </div>

                <div class="card support-card">
                    <h4>Soporte del Sistema</h4>
                    <p class="muted" style="font-size: 0.9rem; margin-top: 10px; line-height: 1.6;">
                        Si tienes problemas con la sincronización de stock, contacta al soporte técnico o verifica tu conexión.
                    </p>
                </div>
            </div>

        </div>

    @else
        {{-- ═══════════════════════════════════════════════ --}}
        {{-- VISTA USUARIO NORMAL — solo sus datos de cuenta --}}
        {{-- ═══════════════════════════════════════════════ --}}

        <div class="card" style="max-width: 640px;">
            <h3 style="margin-bottom: 24px;">Mis datos de cuenta</h3>

            <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table style="width: 100%; border-collapse: collapse; min-width: 260px;">
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 12px 8px; color: var(--text-muted); font-weight: 600; width: 40%;">C.I.</td>
                        <td style="padding: 12px 8px;">{{ Auth::user()->ci }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 12px 8px; color: var(--text-muted); font-weight: 600;">Nombre</td>
                        <td style="padding: 12px 8px;">{{ Auth::user()->nombre }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 12px 8px; color: var(--text-muted); font-weight: 600;">Apellido</td>
                        <td style="padding: 12px 8px;">{{ Auth::user()->apellido }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 12px 8px; color: var(--text-muted); font-weight: 600;">Correo electrónico</td>
                        <td style="padding: 12px 8px;">{{ Auth::user()->email }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 12px 8px; color: var(--text-muted); font-weight: 600;">Teléfono</td>
                        <td style="padding: 12px 8px;">{{ Auth::user()->telefono ?? '—' }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 12px 8px; color: var(--text-muted); font-weight: 600;">Sexo</td>
                        <td style="padding: 12px 8px;">
                            @if(Auth::user()->sexo === 'M') Masculino
                            @elseif(Auth::user()->sexo === 'F') Femenino
                            @else {{ Auth::user()->sexo ?? '—' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 8px; color: var(--text-muted); font-weight: 600;">Domicilio</td>
                        <td style="padding: 12px 8px;">{{ Auth::user()->domicilio ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>
            </div>

            <div style="margin-top: 24px;">
                <a href="{{ route('profile.edit') }}" class="btn-save" style="text-decoration: none; display: inline-block;">Editar mi perfil</a>
            </div>
        </div>

    @endif
</div>
@endsection
