@extends('layouts.ferreteria')

@section('title', 'Bitácora de Auditoría - Ferretería Guisella')
@section('wrap_class', 'wide')

@section('content')
<div class="animate-fade-up">
    <div class="page-header" style="justify-content: flex-start; gap: 15px;">
        <a href="{{ route('inventario') }}" class="btn-circle" style="text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <div>
            <h1 style="margin: 0;">Bitácora de Auditoría</h1>
            <p class="subtitle" style="margin: 0;">Registro inmutable de acciones críticas sobre el sistema</p>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            @if($registros->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Usuario Responsable</th>
                            <th>Acción</th>
                            <th>Tabla Afectada</th>
                            <th>Descripción del Evento</th>
                            <th>Dirección IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registros as $r)
                            <tr>
                                <td style="font-size: 0.85rem;" class="muted">{{ $r->created_at->format('d/m/Y H:i:s') }}</td>
                                <td><strong style="color: var(--text-main);">{{ $r->usuario }}</strong></td>
                                <td>
                                    @if(strtoupper($r->accion) == 'INSERTAR')
                                        <span class="badge bg-insert">{{ $r->accion }}</span>
                                    @elseif(strtoupper($r->accion) == 'ACTUALIZAR')
                                        <span class="badge bg-update">{{ $r->accion }}</span>
                                    @elseif(strtoupper($r->accion) == 'ELIMINAR')
                                        <span class="badge bg-delete">{{ $r->accion }}</span>
                                    @else
                                        <span class="badge">{{ $r->accion }}</span>
                                    @endif
                                </td>
                                <td><code>{{ $r->tabla }}</code></td>
                                <td>{{ $r->descripcion }}</td>
                                <td><span style="font-family: monospace; font-size: 0.8rem; background: var(--bg-light); padding: 2px 6px; border-radius: 4px; border: 1px solid var(--border);">{{ $r->ip }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="padding:40px; text-align:center; background:#f8fafc; border-radius:var(--radius-sm); border:1px dashed var(--border);">
                    <p style="color:var(--text-muted); font-weight:500; font-size:1.1rem;">La bitácora de eventos está vacía.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
