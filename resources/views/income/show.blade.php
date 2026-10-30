@extends('layouts.app')

@section('title', 'Detail Catatan Pendapatan — Arcadia')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-ink">Catatan Pendapatan</h1>
        <div class="flex items-center gap-2">
            @can('update', $record)
                <a href="{{ route('income.edit', $record) }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Edit</a>
            @endcan
            @can('delete', $record)
                <form method="POST" action="{{ route('income.destroy', $record) }}"
                      data-confirm="Yakin ingin menghapus catatan pendapatan mesin &quot;{{ $record->machine->name }}&quot; tanggal {{ $record->date->translatedFormat('d M Y') }}?"
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
                <dd class="mt-1 font-medium text-ink">{{ $record->machine->name }} <span class="font-normal text-ink-muted">({{ $record->machine->serial_number }})</span></dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Tanggal</dt>
                <dd class="mt-1 font-medium text-ink">{{ $record->date->translatedFormat('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Jumlah Permainan</dt>
                <dd class="mt-1 font-medium text-ink">{{ $record->play_count }}</dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Total Pendapatan</dt>
                <dd class="mt-1 font-medium text-ink"><x-ui.rupiah :value="$record->total_income" /></dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Dicatat Oleh</dt>
                <dd class="mt-1 font-medium text-ink">{{ $record->user->name }}</dd>
            </div>
        </dl>
    </div>
@endsection
