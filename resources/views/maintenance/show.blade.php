@extends('layouts.app')

@section('title', 'Detail Log Perawatan — Arcadia')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-ink">Log Perawatan</h1>
        <div class="flex items-center gap-2">
            @can('update', $log)
                <a href="{{ route('maintenance.edit', $log) }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Edit</a>
            @endcan
            @can('delete', $log)
                <form method="POST" action="{{ route('maintenance.destroy', $log) }}"
                      data-confirm="Yakin ingin menghapus log perawatan untuk mesin &quot;{{ $log->machine->name }}&quot; tanggal {{ $log->date->translatedFormat('d M Y') }}?"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-ui border border-danger/30 bg-danger/10 px-4 py-2 text-sm font-medium text-danger hover:bg-danger/20">Hapus</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm text-ink-muted">Mesin</dt>
                <dd class="mt-1 font-medium text-ink">{{ $log->machine->name }} <span class="font-normal text-ink-muted">({{ $log->machine->serial_number }})</span></dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Tanggal</dt>
                <dd class="mt-1 font-medium text-ink">{{ $log->date->translatedFormat('d M Y') }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm text-ink-muted">Masalah</dt>
                <dd class="mt-1 font-medium text-ink">{{ $log->issue }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm text-ink-muted">Tindakan yang Dilakukan</dt>
                <dd class="mt-1 font-medium text-ink">{{ $log->action_taken ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Biaya</dt>
                <dd class="mt-1 font-medium text-ink"><x-ui.rupiah :value="$log->cost" /></dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Status</dt>
                <dd class="mt-1"><x-ui.log-status :status="$log->status" /></dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Dicatat Oleh</dt>
                <dd class="mt-1 font-medium text-ink">{{ $log->user->name }}</dd>
            </div>
        </dl>
    </div>
@endsection
