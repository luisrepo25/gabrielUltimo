@props(['accion'])

@php
    $accionUpper = strtoupper((string) $accion);
    $badgeClass = match (true) {
        str_contains($accionUpper, 'INSERT') => 'bg-insert',
        str_contains($accionUpper, 'UPDATE') || str_contains($accionUpper, 'MODIF') => 'bg-update',
        str_contains($accionUpper, 'DELETE') || str_contains($accionUpper, 'ELIM') => 'bg-delete',
        default => 'bg-insert',
    };
@endphp

<span {{ $attributes->merge(['class' => "badge {$badgeClass}"]) }}>{{ $accion }}</span>
