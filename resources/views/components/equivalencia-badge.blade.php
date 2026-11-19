@props(['equivalencia'])

@php
    $config = match ($equivalencia) {
        'AU' => ['clase' => 'bg-success', 'texto' => 'AU - Autónomo'],
        'DE' => ['clase' => 'bg-info text-dark', 'texto' => 'DE - Destacado'],
        'SA' => ['clase' => 'bg-warning text-dark', 'texto' => 'SA - Suficiente'],
        default => ['clase' => 'bg-danger', 'texto' => 'NA - No Acreditado'],
    };
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $config['clase']]) }}>
    {{ $config['texto'] }}
</span>
