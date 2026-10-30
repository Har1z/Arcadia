@props(['color' => 'muted'])

@php
    // SECURITY: color is matched against a fixed whitelist — anything else
    // falls back to the neutral style.
    $classes = match ($color) {
        'success' => 'bg-success/10 text-success border-success/25',
        'warning' => 'bg-warning/10 text-warning border-warning/25',
        'danger' => 'bg-danger/10 text-danger border-danger/25',
        'info' => 'bg-info/10 text-info border-info/25',
        'accent' => 'bg-accent-soft text-accent border-accent/25',
        default => 'bg-surface-alt text-ink-muted border-line',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold whitespace-nowrap '.$classes]) }}>
    {{ $slot }}
</span>
