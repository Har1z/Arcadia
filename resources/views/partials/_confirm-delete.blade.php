{{-- Modal konfirmasi hapus yang dipakai bersama oleh semua halaman.
     Form dengan atribut data-confirm="pesan" akan membuka modal ini
     sebelum benar-benar dikirim (lihat resources/js/app.js). --}}
<div id="confirm-delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-scrim/70 p-4" role="dialog" aria-modal="true" aria-labelledby="confirm-delete-title">
    <div class="w-full max-w-md rounded-ui border border-line bg-surface p-5 shadow-card">
        <h2 id="confirm-delete-title" class="font-semibold text-ink">Konfirmasi Hapus</h2>
        <p id="confirm-delete-message" class="mt-2 text-sm text-ink-muted"></p>

        <div class="mt-5 flex justify-end gap-2">
            <button type="button" data-confirm-cancel class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Batal</button>
            <button type="button" data-confirm-yes class="rounded-ui border border-danger/30 bg-danger/10 px-4 py-2 text-sm font-medium text-danger hover:bg-danger/20">Hapus</button>
        </div>
    </div>
</div>
