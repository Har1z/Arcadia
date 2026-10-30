@extends('layouts.app')

@section('title', 'Dashboard — Arcadia')

{{-- TODO: all numbers below are static placeholders until the real
     DashboardController aggregates them from the seeded data. --}}

@section('content')
    <h1 class="text-2xl font-bold text-ink">Dashboard</h1>
    <p class="mt-1 text-sm text-ink-muted">Ringkasan kondisi mesin dan pendapatan.</p>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Total Mesin', 'value' => '14', 'color' => 'text-accent'],
            ['label' => 'Mesin Aktif', 'value' => '11', 'color' => 'text-success'],
            ['label' => 'Pendapatan Bulan Ini', 'value' => 'Rp22.602.000', 'color' => 'text-info'],
            ['label' => 'Perawatan Terbuka', 'value' => '4', 'color' => 'text-warning'],
        ] as $card)
            <div class="rounded-ui border border-line bg-surface p-5 shadow-card">
                <p class="text-sm text-ink-muted">{{ $card['label'] }}</p>
                <p class="mt-2 text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-ui border border-line bg-surface shadow-card">
            <div class="border-b border-line px-5 py-4">
                <h2 class="font-semibold text-ink">Perawatan Terbuka</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs font-semibold uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3">Mesin</th>
                            <th class="px-5 py-3">Masalah</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <tr class="hover:bg-surface-alt/60">
                            <td class="px-5 py-3 font-medium">Taiko no Tatsujin</td>
                            <td class="px-5 py-3 text-ink-muted">Drum kiri suaranya berat</td>
                            <td class="px-5 py-3"><x-ui.badge color="warning">Terbuka</x-ui.badge></td>
                        </tr>
                        <tr class="hover:bg-surface-alt/60">
                            <td class="px-5 py-3 font-medium">Tekken 7 FR</td>
                            <td class="px-5 py-3 text-ink-muted">Tombol P1 kadang ga ke input</td>
                            <td class="px-5 py-3"><x-ui.badge color="warning">Terbuka</x-ui.badge></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-ui border border-line bg-surface shadow-card">
            <div class="border-b border-line px-5 py-4">
                <h2 class="font-semibold text-ink">Pendapatan Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs font-semibold uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3">Mesin</th>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <tr class="hover:bg-surface-alt/60">
                            <td class="px-5 py-3 font-medium">Wangan Midnight Maximum Tune</td>
                            <td class="px-5 py-3 text-ink-muted">29 Okt</td>
                            <td class="px-5 py-3 text-right font-medium">Rp304.000</td>
                        </tr>
                        <tr class="hover:bg-surface-alt/60">
                            <td class="px-5 py-3 font-medium">Initial D Arcade Stage</td>
                            <td class="px-5 py-3 text-ink-muted">29 Okt</td>
                            <td class="px-5 py-3 text-right font-medium">Rp190.000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Simple CSS bars, no chart library (per plan). --}}
    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        <h2 class="font-semibold text-ink">Mesin per Status</h2>
        <div class="mt-4 space-y-3">
            @foreach ([
                ['label' => 'Aktif', 'count' => 11, 'class' => 'bg-success'],
                ['label' => 'Perawatan', 'count' => 2, 'class' => 'bg-warning'],
                ['label' => 'Rusak', 'count' => 1, 'class' => 'bg-danger'],
                ['label' => 'Ditarik', 'count' => 1, 'class' => 'bg-line'],
            ] as $bar)
                <div class="flex items-center gap-3 text-sm">
                    <span class="w-24 shrink-0 text-ink-muted">{{ $bar['label'] }}</span>
                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-surface-alt">
                        <div class="h-full rounded-full {{ $bar['class'] }}" style="width: {{ round($bar['count'] / 14 * 100) }}%"></div>
                    </div>
                    <span class="w-8 text-right font-medium">{{ $bar['count'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endsection
