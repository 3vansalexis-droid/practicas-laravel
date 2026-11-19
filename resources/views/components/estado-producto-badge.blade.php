@props(['estado'])

@php
    $clase = $estado === 'Disponible' ? 'bg-success' : 'bg-danger';
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $clase]) }}>
    {{ $estado }}
</span>
