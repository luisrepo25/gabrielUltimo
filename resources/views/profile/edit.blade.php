@extends('layouts.ferreteria')

@section('title', 'Mi Perfil - Ferretería Guisella')

@section('content')
<div class="animate-fade-up" style="max-width: 700px;">

    <div class="page-header">
        <div>
            <h1>Mi Perfil</h1>
            <p class="subtitle" style="margin: 0;">Actualiza tu información personal y contraseña</p>
        </div>
    </div>

    {{-- Datos personales --}}
    <div class="card" style="margin-bottom: 30px;">
        @include('profile.partials.update-profile-information-form')
    </div>

    {{-- Cambiar contraseña --}}
    <div class="card" style="margin-bottom: 30px;">
        @include('profile.partials.update-password-form')
    </div>

</div>
@endsection
