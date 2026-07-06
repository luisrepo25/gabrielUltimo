@extends('layouts.ferreteria')

@section('title', 'Mantenimiento de Maquinaria - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="display: flex; flex-direction: column; gap: 24px;">

    <!-- HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="margin: 0; font-size: 2rem;">Mantenimiento de Maquinaria</h1>
            <p class="subtitle" style="margin: 4px 0 0 0;">Programación y seguimiento de mantenimientos preventivos y correctivos.</p>
        </div>
        <a href="{{ route('admin.mantenimientos.create') }}" class="btn-productos" style="display: flex; align-items: center; gap: 8px; font-weight: 700; text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            Programar Mantenimiento
        </a>
    </div>

    <!-- MENSAJES -->
    @if (session('success'))
        <div class="alert alert-success" style="margin: 0;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error" style="margin: 0;">{{ session('error') }}</div>
    @endif

    <!-- TABLA -->
    <div class="card" style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); overflow-x: auto;">
        @if($mantenimientos->count() > 0)
            <table class="fg-table" style="width: 100%; border-collapse: collapse; min-width: 800px;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border); text-align: left; background: #F9FAFB;">
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">ID</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Maquinaria</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Tipo</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Fecha Inicio</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Fecha Fin</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Costo</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151;">Estado</th>
                        <th style="padding: 12px 16px; font-weight: 800; color: #374151; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mantenimientos as $mant)
                        <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='white'">
                            <td style="padding: 14px 16px; font-weight: 800; color: #1F2937;">{{ $mant->id }}</td>
                            <td style="padding: 14px 16px;">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 700; color: #374151;">{{ $mant->producto->nombre ?? 'N/A' }}</span>
                                    <span style="font-size: 0.75rem; color: var(--text-light);">ID: {{ $mant->producto->idproducto ?? '-' }}{{ $mant->producto->modelo ? ' • Mod: ' . $mant->producto->modelo : '' }} • Cant: {{ $mant->cantidad }}</span>
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                @if($mant->tipo === 'Preventivo')
                                    <span style="background: rgba(59, 130, 246, 0.1); color: #3B82F6; font-weight: 700; font-size: 0.8rem; padding: 4px 10px; border-radius: 6px;">🔧 Preventivo</span>
                                @else
                                    <span style="background: rgba(245, 158, 11, 0.1); color: #F59E0B; font-weight: 700; font-size: 0.8rem; padding: 4px 10px; border-radius: 6px;">⚠️ Correctivo</span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; color: var(--text-light); font-size: 0.9rem;">
                                {{ $mant->fecha_inicio ? $mant->fecha_inicio->format('d/m/Y') : '-' }}
                            </td>
                            <td style="padding: 14px 16px; color: var(--text-light); font-size: 0.9rem;">
                                {{ $mant->fecha_fin ? $mant->fecha_fin->format('d/m/Y') : 'Sin definir' }}
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: #1F2937;">
                                {{ number_format($mant->costo, 2) }} Bs.
                            </td>
                            <td style="padding: 14px 16px;">
                                @if($mant->estado === 'Programado')
                                    <span style="background: rgba(59, 130, 246, 0.1); color: #3B82F6; font-weight: 700; font-size: 0.8rem; padding: 4px 10px; border-radius: 6px;">📅 Programado</span>
                                @elseif($mant->estado === 'En curso')
                                    <span style="background: rgba(245, 158, 11, 0.1); color: #F59E0B; font-weight: 700; font-size: 0.8rem; padding: 4px 10px; border-radius: 6px;">🔄 En curso</span>
                                @else
                                    <span style="background: rgba(16, 185, 129, 0.1); color: #10B981; font-weight: 700; font-size: 0.8rem; padding: 4px 10px; border-radius: 6px;">✅ Finalizado</span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <a href="{{ route('admin.mantenimientos.edit', $mant->id) }}" style="color: #00AF9A; font-weight: 700; text-decoration: none; font-size: 0.85rem;">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 20px;">{{ $mantenimientos->links() }}</div>
        @else
            <div style="text-align: center; padding: 50px 20px; color: var(--text-light); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px;">
                <div style="background: rgba(0, 175, 154, 0.08); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #00AF9A;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: #374151;">Sin mantenimientos registrados</h3>
                <p style="margin: 0; font-size: 0.9rem; max-width: 320px; line-height: 1.5;">Programa el primer mantenimiento de maquinaria desde el botón superior.</p>
            </div>
        @endif
    </div>
</div>
@endsection
