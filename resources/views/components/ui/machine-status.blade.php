@props(['status'])

@php
    $colors = [
        App\Models\Machine::STATUS_ACTIVE => 'success',
        App\Models\Machine::STATUS_MAINTENANCE => 'warning',
        App\Models\Machine::STATUS_BROKEN => 'danger',
    ];
    // Retired (and unknown) statuses fall back to the neutral badge style.
    $color = $colors[$status] ?? 'muted';
@endphp

{{-- Status mesin -> label Indonesia + warna badge yang konsisten di semua halaman. --}}
<x-ui.badge :color="$color">
    {{ App\Models\Machine::STATUS_LABELS[$status] ?? strtoupper($status) }}
</x-ui.badge>
