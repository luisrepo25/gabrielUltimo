@extends('layouts.ferreteria')

@section('title', 'Compra Exitosa - Ferretería Guisella')

@section('content')
    <div class="card animate-fade-up text-center" style="padding: 60px 20px; max-width: 600px; margin: 0 auto;">
        <div style="background: var(--success-light); color: var(--success); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        </div>
        
        <h1 style="color: var(--success); margin-bottom: 16px;">¡Compra Hecha!</h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 30px;">
            Tu pedido ha sido registrado con éxito. 
            <br>
            <span style="font-size: 0.95rem; display: inline-block; margin-top: 10px;">Más adelante se integrará un método de pago. Por ahora, nos pondremos en contacto contigo para coordinar la entrega.</span>
        </p>

        <a href="{{ url('/') }}" class="btn-save">Volver al Catálogo</a>
    </div>
@endsection
