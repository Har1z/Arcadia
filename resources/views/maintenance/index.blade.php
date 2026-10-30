@extends('layouts.app')

@section('title', 'Perawatan — Arcadia')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-ink">Perawatan</h1>
        <a href="{{ route('maintenance.create') }}" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Tambah Log Perawatan</a>
    </div>

    {{-- Filter: pencarian bebas + filter status. Semua nilai lolos validasi
         server (lihat MaintenanceController::index) sebelum dipakai di query.
         Staff hanya melihat log miliknya sendiri (scope di controller). --}}
    <form method="GET" action="{{ route('maintenance.index') }}" class="mt-6 flex flex-wrap items-end gap-3 rounded-ui border border-line bg-surface p-4 shadow-card">
        <div class="min-w-48 flex-1">
            <label for="q" class="mb-1 block text-sm font-medium text-ink">Cari</label>
            <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Masalah atau nama mesin"
                   class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30"
            >
        </div>
        <div>
            <label for="status" class="mb-1 block text-sm font-medium text-ink">Status</label>
            <select id="status" name="status"
                    class="rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30"
            >
                <option value="">Semua Status</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Filter</button>
            @if (request('q') || request('status'))
                <a href="{{ route('maintenance.index') }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Reset</a>
            @endif
        </div>
    </form>

    @if ($errors->any())
        <p class="mt-2 text-sm text-danger" role="alert">{{ $errors->first() }}</p>
    @endif

    <div class="mt-6 rounded-ui border border-line bg-surface shadow-card">
        @if ($logs->isEmpty())
            <div class="m-4 rounded-ui border border-dashed border-line bg-bg-soft px-6 py-10 text-center">
                <p class="font-medium text-ink">Belum ada log perawatan</p>
                <p class="mt-1 text-sm text-ink-muted">Catat masalah mesin agar riwayat perawatan terlacak.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs font-semibold uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3">Mesin</th>
                            <th class="px-5 py-3">Masalah</th>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3 text-right">Biaya</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Dicatat Oleh</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-surface-alt/60">
                                <td class="px-5 py-3 font-medium">
                                    <a href="{{ route('maintenance.show', $log) }}" class="hover:text-accent">{{ $log->machine->name }}</a>
                                </td>
                                <td class="max-w-xs truncate px-5 py-3 text-ink-muted" title="{{ $log->issue }}">{{ $log->issue }}</td>
                                <td class="px-5 py-3 text-ink-muted">{{ $log->date->translatedFormat('d M Y') }}</td>
                                <td class="px-5 py-3 text-right"><x-ui.rupiah :value="$log->cost" /></td>
                                <td class="px-5 py-3"><x-ui.log-status :status="$log->status" /></td>
                                <td class="px-5 py-3 text-ink-muted">{{ $log->user->name }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        {{-- SECURITY: edit/delete only render for admins or the record owner
                                             (same rule as the policy that guards the routes). --}}
                                        @can('update', $log)
                                            <a href="{{ route('maintenance.edit', $log) }}" class="font-medium text-accent hover:underline">Edit</a>
                                        @endcan
                                        @can('delete', $log)
                                            {{-- data-confirm opens the shared delete modal before submitting. --}}
                                            <form method="POST" action="{{ route('maintenance.destroy', $log) }}"
                                                  data-confirm="Yakin ingin menghapus log perawatan untuk mesin &quot;{{ $log->machine->name }}&quot; tanggal {{ $log->date->translatedFormat('d M Y') }}?"
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

    @include('partials._pagination', ['paginator' => $logs])
@endsection
