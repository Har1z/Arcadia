@props(['value'])

{{-- Format rupiah tanpa desimal: 16000 -> Rp16.000 --}}
{{ 'Rp'.number_format((float) $value, 0, ',', '.') }}
