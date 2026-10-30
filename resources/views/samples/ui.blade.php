@extends('layouts.app')

@section('title', 'Contoh Komponen — Arcadia')

{{-- Halaman verifikasi tema + referensi komponen. Tidak masuk menu
     sidebar dan boleh dihapus sebelum deploy (tahap deployment). --}}

@section('content')
    <h1 class="text-2xl font-bold text-ink">Contoh Komponen</h1>
    <p class="mt-1 text-sm text-ink-muted">Semua warna hanya dari design token (light &amp; dark).</p>

    <div class="mt-6 space-y-6">
        <section class="rounded-ui border border-line bg-surface p-5 shadow-card">
            <h2 class="mb-4 font-semibold text-ink">Tombol</h2>
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Simpan</button>
                <button type="button" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Batal</button>
                <button type="button" class="rounded-ui border border-danger/30 bg-danger/10 px-4 py-2 text-sm font-medium text-danger hover:bg-danger/20">Hapus</button>
                <button type="button" disabled class="rounded-ui bg-accent/50 px-4 py-2 text-sm font-medium text-on-accent">Nonaktif</button>
            </div>
        </section>

        <section class="rounded-ui border border-line bg-surface p-5 shadow-card">
            <h2 class="mb-4 font-semibold text-ink">Badge Status Mesin &amp; Perawatan</h2>
            <div class="flex flex-wrap items-center gap-3">
                <x-ui.badge color="success">Aktif</x-ui.badge>
                <x-ui.badge color="warning">Perawatan</x-ui.badge>
                <x-ui.badge color="danger">Rusak</x-ui.badge>
                <x-ui.badge>Ditarik</x-ui.badge>
                <x-ui.badge color="info">Terbuka</x-ui.badge>
                <x-ui.badge color="success">Selesai</x-ui.badge>
                <x-ui.badge color="accent">Admin</x-ui.badge>
            </div>
        </section>

        <section class="rounded-ui border border-line bg-surface shadow-card">
            <h2 class="border-b border-line px-5 py-4 font-semibold text-ink">Tabel</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs font-semibold uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3">Mesin</th>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Harga per Main</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <tr class="hover:bg-surface-alt/60">
                            <td class="px-5 py-3 font-medium">Wangan Midnight Maximum Tune</td>
                            <td class="px-5 py-3 text-ink-muted">Racing</td>
                            <td class="px-5 py-3"><x-ui.badge color="warning">Perawatan</x-ui.badge></td>
                            <td class="px-5 py-3 text-right">Rp16.000</td>
                        </tr>
                        <tr class="hover:bg-surface-alt/60">
                            <td class="px-5 py-3 font-medium">Initial D Arcade Stage</td>
                            <td class="px-5 py-3 text-ink-muted">Racing</td>
                            <td class="px-5 py-3"><x-ui.badge color="success">Aktif</x-ui.badge></td>
                            <td class="px-5 py-3 text-right">Rp10.000</td>
                        </tr>
                        <tr class="hover:bg-surface-alt/60">
                            <td class="px-5 py-3 font-medium">Mortal Kombat 4</td>
                            <td class="px-5 py-3 text-ink-muted">Fighting</td>
                            <td class="px-5 py-3"><x-ui.badge>Ditarik</x-ui.badge></td>
                            <td class="px-5 py-3 text-right">Rp10.000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-ui border border-line bg-surface p-5 shadow-card">
            <h2 class="mb-4 font-semibold text-ink">Formulir &amp; Error Inline</h2>
            <div class="grid max-w-xl grid-cols-1 gap-4">
                <div>
                    <label for="sample-name" class="mb-1 block text-sm font-medium text-ink">Nama Mesin</label>
                    <input id="sample-name" type="text" value="Wangan Midnight"
                           class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30"
                    >
                </div>
                <div>
                    <label for="sample-serial" class="mb-1 block text-sm font-medium text-ink">Nomor Seri</label>
                    <input id="sample-serial" type="text" value="ARC-9999" aria-invalid="true"
                           class="w-full rounded-ui border border-danger bg-surface px-3 py-2 text-sm text-ink focus:border-danger focus:outline-none focus:ring-2 focus:ring-danger/30"
                    >
                    <p class="mt-1 text-sm text-danger">Nomor seri sudah digunakan mesin lain.</p>
                </div>
                <div>
                    <label for="sample-status" class="mb-1 block text-sm font-medium text-ink">Status</label>
                    <select id="sample-status"
                            class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30"
                    >
                        <option>Aktif</option>
                        <option>Perawatan</option>
                        <option>Rusak</option>
                        <option>Ditarik</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="rounded-ui border border-line bg-surface p-5 shadow-card">
            <h2 class="mb-4 font-semibold text-ink">Alert</h2>
            <div class="space-y-3">
                <div class="rounded-ui border border-success/25 bg-success/10 px-4 py-3 text-sm text-success" role="status">Data mesin berhasil disimpan.</div>
                <div class="rounded-ui border border-danger/25 bg-danger/10 px-4 py-3 text-sm text-danger" role="alert">Mesin masih memiliki catatan perawatan.</div>
                <div class="rounded-ui border border-info/25 bg-info/10 px-4 py-3 text-sm text-info" role="status">Gunakan akun demo dari README.</div>
            </div>
        </section>

        <section class="rounded-ui border border-line bg-surface p-5 shadow-card">
            <h2 class="mb-4 font-semibold text-ink">Keadaan Kosong</h2>
            <div class="rounded-ui border border-dashed border-line bg-bg-soft px-6 py-10 text-center">
                <p class="font-medium text-ink">Belum ada data</p>
                <p class="mt-1 text-sm text-ink-muted">Data akan muncul setelah ditambahkan.</p>
                <button type="button" class="mt-4 rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Tambah Data</button>
            </div>
        </section>
    </div>
@endsection
