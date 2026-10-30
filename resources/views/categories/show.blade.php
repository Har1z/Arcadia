@extends('layouts.app')

@section('title', $category->name.' — Arcadia')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-ink">{{ $category->name }}</h1>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('categories.edit', $category) }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Edit</a>
        @endif
    </div>

    <p class="mt-2 text-sm text-ink-muted">{{ $category->description ?? 'Tanpa deskripsi.' }}</p>

    <div class="mt-6 rounded-ui border border-line bg-surface shadow-card">
        <div class="border-b border-line px-5 py-4">
            <h2 class="font-semibold text-ink">Mesin dalam Kategori Ini ({{ $category->machines->count() }})</h2>
        </div>

        @if ($category->machines->isEmpty())
            <div class="m-4 rounded-ui border border-dashed border-line bg-bg-soft px-6 py-10 text-center">
                <p class="font-medium text-ink">Belum ada mesin</p>
                <p class="mt-1 text-sm text-ink-muted">Belum ada mesin yang menggunakan kategori ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs font-semibold uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3">Nama Mesin</th>
                            <th class="px-5 py-3">Nomor Seri</th>
                            <th class="px-5 py-3">Lokasi</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($category->machines as $machine)
                            <tr class="hover:bg-surface-alt/60">
                                <td class="px-5 py-3 font-medium">
                                    <a href="{{ route('machines.show', $machine) }}" class="hover:text-accent">{{ $machine->name }}</a>
                                </td>
                                <td class="px-5 py-3 text-ink-muted">{{ $machine->serial_number }}</td>
                                <td class="px-5 py-3 text-ink-muted">{{ $machine->location }}</td>
                                <td class="px-5 py-3"><x-ui.machine-status :status="$machine->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
