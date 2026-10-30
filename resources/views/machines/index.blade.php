@extends('layouts.app')

@section('title', 'Mesin — Arcadia')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-ink">Mesin</h1>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('machines.create') }}" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Tambah Mesin</a>
        @endif
    </div>

    {{-- Filter: pencarian bebas + filter status. Semua nilai lolos validasi
         server (lihat MachineController::index) sebelum dipakai di query. --}}
    <form method="GET" action="{{ route('machines.index') }}" class="mt-6 flex flex-wrap items-end gap-3 rounded-ui border border-line bg-surface p-4 shadow-card">
        <div class="min-w-48 flex-1">
            <label for="q" class="mb-1 block text-sm font-medium text-ink">Cari</label>
            <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Nama, nomor seri, atau lokasi"
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
                <a href="{{ route('machines.index') }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Reset</a>
            @endif
        </div>
    </form>

    @if ($errors->any())
        <p class="mt-2 text-sm text-danger" role="alert">{{ $errors->first() }}</p>
    @endif

    <div class="mt-6 rounded-ui border border-line bg-surface shadow-card">
        @if ($machines->isEmpty())
            <div class="m-4 rounded-ui border border-dashed border-line bg-bg-soft px-6 py-10 text-center">
                <p class="font-medium text-ink">Belum ada mesin</p>
                <p class="mt-1 text-sm text-ink-muted">Data mesin akan muncul setelah ditambahkan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs font-semibold uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3">Nama Mesin</th>
                            <th class="px-5 py-3">Nomor Seri</th>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3">Lokasi</th>
                            <th class="px-5 py-3 text-right">Harga per Main</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($machines as $machine)
                            <tr class="hover:bg-surface-alt/60">
                                <td class="px-5 py-3 font-medium">
                                    <a href="{{ route('machines.show', $machine) }}" class="hover:text-accent">{{ $machine->name }}</a>
                                </td>
                                <td class="px-5 py-3 text-ink-muted">{{ $machine->serial_number }}</td>
                                <td class="px-5 py-3 text-ink-muted">{{ $machine->category->name }}</td>
                                <td class="px-5 py-3 text-ink-muted">{{ $machine->location }}</td>
                                <td class="px-5 py-3 text-right"><x-ui.rupiah :value="$machine->price_per_play" /></td>
                                <td class="px-5 py-3"><x-ui.machine-status :status="$machine->status" /></td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('machines.edit', $machine) }}" class="font-medium text-accent hover:underline">Edit</a>
                                        @if (auth()->user()->isAdmin())
                                            {{-- data-confirm opens the shared delete modal before submitting. --}}
                                            <form method="POST" action="{{ route('machines.destroy', $machine) }}"
                                                  data-confirm="Yakin ingin menghapus mesin &quot;{{ $machine->name }}&quot;? Log perawatan dan pendapatan mesin ini ikut terhapus."
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-danger hover:underline">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @include('partials._pagination', ['paginator' => $machines])
@endsection
