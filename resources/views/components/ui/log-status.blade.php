@props(['status'])

@php
    $colors = [
        App\Models\MaintenanceLog::STATUS_OPEN => 'warning',
        App\Models\MaintenanceLog::STATUS_DONE => 'success',
    ];
    // Unknown statuses fall back to the neutral badge style.
    $color = $colors[$status] ?? 'muted';
@endphp

{{-- Status log perawatan -> label Indonesia + warna badge yang konsisten. --}}
<x-ui.badge :color="$color">
    {{ App\Models\MaintenanceLog::STATUS_LABELS[$status] ?? strtoupper($status) }}
</x-ui.badge>
