@extends('layouts.app')

@section('title', 'Pengguna — Arcadia')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-ink">Pengguna</h1>
        <a href="{{ route('users.create') }}" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Tambah Pengguna</a>
    </div>

    {{-- Filter: pencarian nama/email + filter role. Nilai divalidasi
         server (lihat UserController::index) sebelum dipakai di query. --}}
    <form method="GET" action="{{ route('users.index') }}" class="mt-6 flex flex-wrap items-end gap-3 rounded-ui border border-line bg-surface p-4 shadow-card">
        <div class="min-w-48 flex-1">
            <label for="q" class="mb-1 block text-sm font-medium text-ink">Cari</label>
            <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Nama atau email"
                   class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30"
            >
        </div>
        <div>
            <label for="role" class="mb-1 block text-sm font-medium text-ink">Role</label>
            <select id="role" name="role"
                    class="rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30"
            >
                <option value="">Semua Role</option>
                @foreach ($roles as $value => $label)
                    <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Filter</button>
            @if (request('q') || request('role'))
                <a href="{{ route('users.index') }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Reset</a>
            @endif
        </div>
    </form>

    @if ($errors->any())
        <p class="mt-2 text-sm text-danger" role="alert">{{ $errors->first() }}</p>
    @endif

    <div class="mt-6 rounded-ui border border-line bg-surface shadow-card">
        @if ($users->isEmpty())
            <div class="m-4 rounded-ui border border-dashed border-line bg-bg-soft px-6 py-10 text-center">
                <p class="font-medium text-ink">Belum ada pengguna</p>
                <p class="mt-1 text-sm text-ink-muted">Pengguna akan muncul setelah ditambahkan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs font-semibold uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3">Nama</th>
                            <th class="px-5 py-3">Email</th>
                            <th class="px-5 py-3">Role</th>
                            <th class="px-5 py-3">Terdaftar</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($users as $userRow)
                            <tr class="hover:bg-surface-alt/60">
                                <td class="px-5 py-3 font-medium">
                                    {{ $userRow->name }}
                                    @if ($userRow->is(auth()->user()))
                                        <span class="ml-1 text-xs text-ink-muted">(Anda)</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-ink-muted">{{ $userRow->email }}</td>
                                <td class="px-5 py-3">
                                    {{-- SECURITY: badge display-only; access control lives in middleware. --}}
                                    <x-ui.badge :color="$userRow->isAdmin() ? 'accent' : 'muted'">{{ \App\Models\User::ROLE_LABELS[$userRow->role] ?? $userRow->role }}</x-ui.badge>
                                </td>
                                <td class="px-5 py-3 text-ink-muted">{{ $userRow->created_at->translatedFormat('d M Y') }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('users.edit', $userRow) }}" class="font-medium text-accent hover:underline">Edit</a>
                                        {{-- data-confirm opens the shared delete modal before submitting. --}}
                                        <form method="POST" action="{{ route('users.destroy', $userRow) }}"
                                              data-confirm="Yakin ingin menghapus pengguna &quot;{{ $userRow->name }}&quot;? Pengguna dengan catatan perawatan atau pendapatan tidak bisa dihapus."
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-danger hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @include('partials._pagination', ['paginator' => $users])
@endsection
