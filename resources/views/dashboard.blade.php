@extends('layouts.app')

@section('title', 'Dashboard — Arcadia')

@section('content')
    <h1 class="text-2xl font-bold text-ink">Dashboard</h1>
    <p class="mt-1 text-sm text-ink-muted">Ringkasan kondisi mesin dan pendapatan.</p>

    {{-- Semua angka dihitung langsung dari database (lihat
         DashboardController::index) sehingga selalu sesuai data nyata. --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Total Mesin', 'value' => $totalMachines, 'color' => 'text-accent'],
            ['label' => 'Mesin Aktif', 'value' => $activeMachines, 'color' => 'text-success'],
            ['label' => 'Pendapatan Bulan Ini', 'value' => 'Rp'.number_format((float) $monthlyIncome, 0, ',', '.'), 'color' => 'text-info'],
            ['label' => 'Perawatan Terbuka', 'value' => $openMaintenanceCount, 'color' => 'text-warning'],
        ] as $card)
            <div class="rounded-ui border border-line bg-surface p-5 shadow-card">
                <p class="text-sm text-ink-muted">{{ $card['label'] }}</p>
                <p class="mt-2 text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-ui border border-line bg-surface shadow-card">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <h2 class="font-semibold text-ink">Perawatan Terbuka</h2>
                <a href="{{ route('maintenance.index') }}" class="text-sm font-medium text-accent hover:underline">Lihat semua</a>
            </div>
            @if ($openMaintenanceLogs->isEmpty())
                <div class="m-4 rounded-ui border border-dashed border-line bg-bg-soft px-6 py-10 text-center">
                    <p class="font-medium text-ink">Tidak ada perawatan terbuka</p>
                    <p class="mt-1 text-sm text-ink-muted">Semua masalah mesin sudah ditangani.</p>
                </div>
            @else
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
                            @foreach ($openMaintenanceLogs as $log)
                                <tr class="hover:bg-surface-alt/60">
                                    <td class="px-5 py-3 font-medium">
                                        <a href="{{ route('machines.show', $log->machine) }}" class="hover:text-accent">{{ $log->machine->name }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-ink-muted">{{ $log->issue }}</td>
                                    <td class="px-5 py-3"><x-ui.log-status :status="$log->status" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="rounded-ui border border-line bg-surface shadow-card">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <h2 class="font-semibold text-ink">Pendapatan Terbaru</h2>
                <a href="{{ route('income.index') }}" class="text-sm font-medium text-accent hover:underline">Lihat semua</a>
            </div>
            @if ($latestIncomeRecords->isEmpty())
                <div class="m-4 rounded-ui border border-dashed border-line bg-bg-soft px-6 py-10 text-center">
                    <p class="font-medium text-ink">Belum ada pendapatan tercatat</p>
                    <p class="mt-1 text-sm text-ink-muted">Catatan pendapatan akan muncul setelah ditambahkan.</p>
                </div>
            @else
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
                            @foreach ($latestIncomeRecords as $record)
                                <tr class="hover:bg-surface-alt/60">
                                    <td class="px-5 py-3 font-medium">
                                        <a href="{{ route('machines.show', $record->machine) }}" class="hover:text-accent">{{ $record->machine->name }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-ink-muted">{{ $record->date->translatedFormat('d M Y') }}</td>
                                    <td class="px-5 py-3 text-right font-medium"><x-ui.rupiah :value="$record->total_income" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Simple CSS bars, no chart library (per plan). --}}
    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        <h2 class="font-semibold text-ink">Mesin per Status</h2>
        <div class="mt-4 space-y-3">
            @foreach ([
                \App\Models\Machine::STATUS_ACTIVE => 'bg-success',
                \App\Models\Machine::STATUS_MAINTENANCE => 'bg-warning',
                \App\Models\Machine::STATUS_BROKEN => 'bg-danger',
                \App\Models\Machine::STATUS_RETIRED => 'bg-line',
            ] as $status => $barClass)
                @php
                    $count = (int) ($statusCounts[$status] ?? 0);
                    // Guard against division by zero on an empty fleet.
                    $percentage = $totalMachines > 0 ? round($count / $totalMachines * 100) : 0;
                @endphp
                <div class="flex items-center gap-3 text-sm">
                    <span class="w-24 shrink-0 text-ink-muted">{{ \App\Models\Machine::STATUS_LABELS[$status] }}</span>
                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-surface-alt">
                        <div class="h-full rounded-full {{ $barClass }}" style="width: {{ $percentage }}%"></div>
                    </div>
                    <span class="w-8 text-right font-medium">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endsection
