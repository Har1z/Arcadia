@extends('layouts.app')

@section('title', 'Kategori — Arcadia')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-ink">Kategori</h1>
        {{-- SECURITY: staff may view this page read-only; the button is
             hidden for them and the routes are admin-only server-side. --}}
        @if (auth()->user()->isAdmin())
            <a href="{{ route('categories.create') }}" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Tambah Kategori</a>
        @endif
    </div>

    <form method="GET" action="{{ route('categories.index') }}" class="mt-6 flex flex-wrap items-end gap-3 rounded-ui border border-line bg-surface p-4 shadow-card">
        <div class="min-w-48 flex-1">
            <label for="q" class="mb-1 block text-sm font-medium text-ink">Cari</label>
            <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Nama atau deskripsi kategori"
                   class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30"
            >
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Cari</button>
            @if (request('q'))
                <a href="{{ route('categories.index') }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Reset</a>
            @endif
        </div>
    </form>

    @if ($errors->any())
        <p class="mt-2 text-sm text-danger" role="alert">{{ $errors->first() }}</p>
    @endif

    <div class="mt-6 rounded-ui border border-line bg-surface shadow-card">
        @if ($categories->isEmpty())
            <div class="m-4 rounded-ui border border-dashed border-line bg-bg-soft px-6 py-10 text-center">
                <p class="font-medium text-ink">Belum ada kategori</p>
                <p class="mt-1 text-sm text-ink-muted">Data kategori akan muncul setelah ditambahkan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs font-semibold uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3">Nama Kategori</th>
                            <th class="px-5 py-3">Deskripsi</th>
                            <th class="px-5 py-3 text-right">Jumlah Mesin</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($categories as $category)
                            <tr class="hover:bg-surface-alt/60">
                                <td class="px-5 py-3 font-medium">
                                    <a href="{{ route('categories.show', $category) }}" class="hover:text-accent">{{ $category->name }}</a>
                                </td>
                                <td class="px-5 py-3 text-ink-muted">{{ $category->description ?? '—' }}</td>
                                <td class="px-5 py-3 text-right">{{ $category->machines_count }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        @if (auth()->user()->isAdmin())
                                            <a href="{{ route('categories.edit', $category) }}" class="font-medium text-accent hover:underline">Edit</a>
                                            <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                                  data-confirm="Yakin ingin menghapus kategori &quot;{{ $category->name }}&quot;?"
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

    @include('partials._pagination', ['paginator' => $categories])
@endsection
