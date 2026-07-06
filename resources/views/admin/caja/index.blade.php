@extends('layouts.ferreteria')

@section('title', 'Gestión y Arqueo de Caja')

@section('content')
<div class="caja-container" x-data="{ openApertura: false, openCorte: false, currentCajaId: null, currentSaldoEsperado: 0 }">
    
    <div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: var(--primary-dark, #0d9488); margin: 0; font-size: 1.75rem;">Gestión y Arqueo de Caja</h1>
        <button class="btn btn-primary" @click="openApertura = true" style="background-color: var(--primary, #14b8a6); border: none; padding: 0.5rem 1rem; color: white; border-radius: 6px; cursor: pointer; font-weight: 600;">
            Apertura nueva
        </button>
    </div>

    @if(session('success'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 6px; margin-bottom: 1rem; border: 1px solid #a7f3d0;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error_acceso'))
        <div style="background-color: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 6px; margin-bottom: 1rem; border: 1px solid #fecaca;">
            {{ session('error_acceso') }}
        </div>
    @endif

    <div class="table-responsive" style="background: white; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background-color: var(--gray-light, #f3f4f6); text-align: left;">
                <tr>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #374151;">#</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #374151;">Usuario</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #374151;">Monto Inicial</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #374151;">Fecha Apertura</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #374151;">Estatus</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #374151;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cajas as $caja)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 1rem; color: #4b5563;">{{ $caja->id }}</td>
                        <td style="padding: 1rem; color: #4b5563;">{{ $caja->user->nombre ?? 'Desconocido' }}</td>
                        <td style="padding: 1rem; color: #4b5563;">Bs. {{ number_format($caja->monto_apertura, 2) }}</td>
                        <td style="padding: 1rem; color: #4b5563;">{{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y H:i') }}</td>
                        <td style="padding: 1rem;">
                            @if($caja->estado === 'abierta')
                                <span style="background-color: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">Abierta</span>
                            @else
                                <span style="background-color: #f3f4f6; color: #374151; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">Cerrada</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; gap: 0.5rem;">
                                @if($caja->estado === 'abierta')
                                    <button @click="openCorte = true; currentCajaId = {{ $caja->id }}; currentSaldoEsperado = {{ $caja->saldo_esperado }};" 
                                            title="Corte de caja"
                                            style="background: transparent; border: none; cursor: pointer; color: var(--primary, #14b8a6);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                                            <line x1="2" y1="10" x2="22" y2="10"></line>
                                            <path d="M7 15h.01"></path>
                                            <path d="M11 15h2"></path>
                                        </svg>
                                    </button>
                                @else
                                    <a href="{{ route('caja.reporte', $caja->id) }}" title="Descargar reporte" style="color: var(--primary-dark, #0d9488);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 1rem; text-align: center; color: #6b7280;">No hay cajas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 1rem;">
        {{ $cajas->links() }}
    </div>

    {{-- Modal Apertura --}}
    <div x-show="openApertura" style="display: none;" class="modal-backdrop">
        <div class="modal-content" @click.away="openApertura = false">
            <h3 style="margin-top: 0; color: var(--primary-dark, #0d9488);">Apertura de Caja</h3>
            <form action="{{ route('caja.apertura') }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; color: #374151;">Registrar la cantidad inicial (Bs.)</label>
                    <input type="number" name="monto_apertura" step="0.01" min="0" required class="form-input" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" @click="openApertura = false" style="padding: 0.5rem 1rem; border: 1px solid #d1d5db; background: white; border-radius: 4px; cursor: pointer;">Cancelar</button>
                    <button type="submit" style="padding: 0.5rem 1rem; border: none; background: var(--primary, #14b8a6); color: white; border-radius: 4px; cursor: pointer;">Aperturar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Corte --}}
    <div x-show="openCorte" style="display: none;" class="modal-backdrop">
        <div class="modal-content" @click.away="openCorte = false">
            <h3 style="margin-top: 0; color: var(--primary-dark, #0d9488);">Corte de Caja</h3>
            <div style="margin-bottom: 1rem; padding: 1rem; background-color: #f0fdfa; border-radius: 6px; border: 1px solid #ccfbf1;">
                <p style="margin: 0; color: #0f766e; font-weight: 500;">Saldo Esperado: <span x-text="'Bs. ' + Number(currentSaldoEsperado).toFixed(2)"></span></p>
                <small style="color: #0d9488;">(Fondo inicial + Ventas)</small>
            </div>
            
            <form :action="'/caja/corte/' + currentCajaId" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; color: #374151;">Monto Real en Caja (Bs.)</label>
                    <input type="number" name="monto_real" step="0.01" min="0" required class="form-input" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" @click="openCorte = false" style="padding: 0.5rem 1rem; border: 1px solid #d1d5db; background: white; border-radius: 4px; cursor: pointer;">Cancelar</button>
                    <button type="submit" style="padding: 0.5rem 1rem; border: none; background: var(--primary, #14b8a6); color: white; border-radius: 4px; cursor: pointer;">Realizar Corte</button>
                </div>
            </form>
        </div>
    </div>

</div>

<style>
    .modal-backdrop {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex; align-items: center; justify-content: center;
        z-index: 1000;
    }
    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .form-input:focus {
        outline: none;
        border-color: var(--primary, #14b8a6);
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.2);
    }
</style>
@endsection
