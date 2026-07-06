@extends('layouts.ferreteria')

@section('title', 'Mis Cotizaciones Guardadas - Ferretería Guisella')

@section('content')
    <div class="animate-fade-up">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 8px;">
            <div>
                <h1 style="margin: 0;">Mis Cotizaciones Guardadas</h1>
                <p class="subtitle">Revisa tus cotizaciones anteriores y cárgalas en tu carrito cuando lo desees.</p>
            </div>
            <a href="{{ route('carrito.index') }}" style="display: inline-flex; align-items: center; gap: 8px; background: white; color: var(--text-main); border: 2px solid var(--border); padding: 12px 20px; border-radius: 12px; font-weight: 700; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--text-main)';">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Volver al Carrito
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if($cotizaciones->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach($cotizaciones as $cotizacion)
                <div class="card animate-fade-up" style="animation-delay: {{ $loop->index * 0.05 }}s; overflow: hidden;">
                    {{-- Cabecera de la cotización --}}
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                        <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                            {{-- Ícono y número --}}
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                            </div>
                            <div>
                                <div style="font-weight: 800; font-size: 1.15rem; color: var(--text-main);">Cotización #{{ $cotizacion->id }}</div>
                                <div style="color: var(--text-light); font-size: 0.9rem; margin-top: 2px;">
                                    📅 {{ \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y') }}
                                    &nbsp;·&nbsp;
                                    🕐 {{ $cotizacion->created_at->format('H:i') }}
                                    &nbsp;·&nbsp;
                                    📦 {{ $cotizacion->detalles->count() }} producto(s)
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            {{-- Total --}}
                            <div style="background: linear-gradient(135deg, #ECFDF5, #D1FAE5); padding: 10px 20px; border-radius: 10px; font-weight: 900; font-size: 1.2rem; color: #059669;">
                                {{ number_format($cotizacion->total, 2) }} Bs.
                            </div>

                            {{-- Botón desplegable --}}
                            <button onclick="toggleDetalles({{ $cotizacion->id }})" id="btn-toggle-{{ $cotizacion->id }}" style="display: flex; align-items: center; gap: 6px; background: white; color: #6366F1; border: 2px solid #6366F1; padding: 10px 16px; border-radius: 10px; font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#6366F1'; this.style.color='white';" onmouseout="this.style.background='white'; this.style.color='#6366F1';">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" id="icon-toggle-{{ $cotizacion->id }}" style="transition: transform 0.3s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                Ver Productos
                            </button>

                            {{-- Botón cargar en carrito --}}
                            <form action="{{ route('cotizaciones.cargar', $cotizacion->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" style="display: flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #00AF9A, #00C9B1); color: white; border: none; padding: 10px 16px; border-radius: 10px; font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(0, 175, 154, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 18px rgba(0, 175, 154, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0, 175, 154, 0.3)';">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                    Cargar en Carrito
                                </button>
                            </form>

                            {{-- Botón eliminar --}}
                            <form action="{{ route('cotizaciones.eliminar', $cotizacion->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta cotización?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="display: flex; align-items: center; gap: 6px; background: white; color: #EF4444; border: 2px solid #FCA5A5; padding: 10px 14px; border-radius: 10px; font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#EF4444'; this.style.color='white'; this.style.borderColor='#EF4444';" onmouseout="this.style.background='white'; this.style.color='#EF4444'; this.style.borderColor='#FCA5A5';">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Detalles desplegables --}}
                    <div id="detalles-{{ $cotizacion->id }}" style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.3s ease; padding: 0 0;">
                        <div style="margin-top: 20px; border-top: 2px solid var(--border); padding-top: 16px;">
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th class="text-center">Precio Unitario</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-right">Subtotal</th>
                                            <th class="text-center">Stock Actual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cotizacion->detalles as $detalle)
                                            <tr>
                                                <td>
                                                    <div style="font-weight: 700; color: var(--text-main);">
                                                        {{ $detalle->producto ? $detalle->producto->nombre : 'Producto no disponible' }}
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ number_format($detalle->precio_unitario, 2) }} Bs.</td>
                                                <td class="text-center">
                                                    <span style="background: #EEF2FF; color: #6366F1; padding: 4px 12px; border-radius: 8px; font-weight: 700;">
                                                        {{ $detalle->cantidad }}
                                                    </span>
                                                </td>
                                                <td class="text-right" style="font-weight: 800; color: var(--primary);">
                                                    {{ number_format($detalle->precio_unitario * $detalle->cantidad, 2) }} Bs.
                                                </td>
                                                <td class="text-center">
                                                    @if($detalle->producto)
                                                        @if($detalle->producto->cantidad > 0)
                                                            <span style="background: #ECFDF5; color: #059669; padding: 4px 12px; border-radius: 8px; font-weight: 700;">
                                                                {{ $detalle->producto->cantidad }} disponible(s)
                                                            </span>
                                                        @else
                                                            <span style="background: #FEF2F2; color: #EF4444; padding: 4px 12px; border-radius: 8px; font-weight: 700;">
                                                                Sin stock
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span style="background: #FEF3C7; color: #D97706; padding: 4px 12px; border-radius: 8px; font-weight: 700;">
                                                            No existe
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card animate-fade-up text-center" style="padding: 60px 20px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--text-light)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 20px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            <h2 style="color: var(--text-muted); margin-bottom: 16px;">No tienes cotizaciones guardadas</h2>
            <p style="color: var(--text-light); margin-bottom: 24px;">Agrega productos al carrito y guarda una cotización para revisarla más tarde.</p>
            <a href="{{ route('carrito.index') }}" class="btn-save">Ir al Carrito</a>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    function toggleDetalles(id) {
        const detalles = document.getElementById('detalles-' + id);
        const icon = document.getElementById('icon-toggle-' + id);
        const btn = document.getElementById('btn-toggle-' + id);

        if (detalles.style.maxHeight === '0px' || detalles.style.maxHeight === '') {
            detalles.style.maxHeight = detalles.scrollHeight + 'px';
            icon.style.transform = 'rotate(180deg)';
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.3s; transform: rotate(180deg);"><polyline points="6 9 12 15 18 9"></polyline></svg> Ocultar Productos';
        } else {
            detalles.style.maxHeight = '0px';
            icon.style.transform = 'rotate(0deg)';
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.3s;"><polyline points="6 9 12 15 18 9"></polyline></svg> Ver Productos';
        }
    }
</script>
@endpush
