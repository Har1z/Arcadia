@extends('layouts.app')

@section('title', 'Pendapatan — Arcadia')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-ink">Pendapatan</h1>
        <a href="{{ route('income.create') }}" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Tambah Pendapatan</a>
    </div>

    {{-- Filter: pencarian berdasarkan nama/nomor seri mesin. Nilai lolos
         validasi server (lihat IncomeController::index) sebelum dipakai.
         Staff hanya melihat catatan miliknya sendiri (scope di controller). --}}
    <form method="GET" action="{{ route('income.index') }}" class="mt-6 flex flex-wrap items-end gap-3 rounded-ui border border-line bg-surface p-4 shadow-card">
        <div class="min-w-48 flex-1">
            <label for="q" class="mb-1 block text-sm font-medium text-ink">Cari</label>
            <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Nama atau nomor seri mesin"
                   class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30"
            >
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Filter</button>
            @if (request('q'))
                <a href="{{ route('income.index') }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Reset</a>
            @endif
        </div>
    </form>

    @if ($errors->any())
        <p class="mt-2 text-sm text-danger" role="alert">{{ $errors->first() }}</p>
    @endif

    <div class="mt-6 rounded-ui border border-line bg-surface shadow-card">
        @if ($records->isEmpty())
            <div class="m-4 rounded-ui border border-dashed border-line bg-bg-soft px-6 py-10 text-center">
                <p class="font-medium text-ink">Belum ada catatan pendapatan</p>
                <p class="mt-1 text-sm text-ink-muted">Catat pendapatan harian tiap mesin untuk laporan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs font-semibold uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3">Mesin</th>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3 text-right">Jumlah Permainan</th>
                            <th class="px-5 py-3 text-right">Total Pendapatan</th>
                            <th class="px-5 py-3">Dicatat Oleh</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($records as $record)
                            <tr class="hover:bg-surface-alt/60">
                                <td class="px-5 py-3 font-medium">
                                    <a href="{{ route('income.show', $record) }}" class="hover:text-accent">{{ $record->machine->name }}</a>
                                </td>
                                <td class="px-5 py-3 text-ink-muted">{{ $record->date->translatedFormat('d M Y') }}</td>
                                <td class="px-5 py-3 text-right">{{ $record->play_count }}</td>
                                <td class="px-5 py-3 text-right font-medium"><x-ui.rupiah :value="$record->total_income" /></td>
                                <td class="px-5 py-3 text-ink-muted">{{ $record->user->name }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        {{-- SECURITY: edit/delete only render for admins or the record owner
                                             (same rule as the policy that guards the routes). --}}
                                        @can('update', $record)
                                            <a href="{{ route('income.edit', $record) }}" class="font-medium text-accent hover:underline">Edit</a>
                                        @endcan
                                        @can('delete', $record)
                                            {{-- data-confirm opens the shared delete modal before submitting. --}}
                                            <form method="POST" action="{{ route('income.destroy', $record) }}"
                                                  data-confirm="Yakin ingin menghapus catatan pendapatan mesin &quot;{{ $record->machine->name }}&quot; tanggal {{ $record->date->translatedFormat('d M Y') }}?"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-danger hover:underline">Hapus</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @include('partials._pagination', ['paginator' => $records])
@endsection
