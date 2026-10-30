@extends('layouts.app')

@section('title', $machine->name.' — Arcadia')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-ink">{{ $machine->name }}</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('machines.edit', $machine) }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Edit</a>
            @if (auth()->user()->isAdmin())
                <form method="POST" action="{{ route('machines.destroy', $machine) }}"
                      data-confirm="Yakin ingin menghapus mesin &quot;{{ $machine->name }}&quot;? Log perawatan dan pendapatan mesin ini ikut terhapus."
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-ui border border-danger/30 bg-danger/10 px-4 py-2 text-sm font-medium text-danger hover:bg-danger/20">Hapus</button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm text-ink-muted">Nomor Seri</dt>
                <dd class="mt-1 font-medium text-ink">{{ $machine->serial_number }}</dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Kategori</dt>
                <dd class="mt-1 font-medium text-ink">{{ $machine->category->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Lokasi</dt>
                <dd class="mt-1 font-medium text-ink">{{ $machine->location }}</dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Status</dt>
                <dd class="mt-1"><x-ui.machine-status :status="$machine->status" /></dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Tanggal Pembelian</dt>
                <dd class="mt-1 font-medium text-ink">{{ $machine->purchase_date->translatedFormat('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Harga Beli</dt>
                <dd class="mt-1 font-medium text-ink"><x-ui.rupiah :value="$machine->purchase_price" /></dd>
            </div>
            <div>
                <dt class="text-sm text-ink-muted">Harga per Main</dt>
                <dd class="mt-1 font-medium text-ink"><x-ui.rupiah :value="$machine->price_per_play" /></dd>
            </div>
        </dl>
    </div>
@endsection
