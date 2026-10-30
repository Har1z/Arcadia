@php $log = $log ?? null; @endphp

{{-- Form bersama untuk tambah ($log kosong) dan ubah log perawatan.
      user_id tidak pernah dikirim dari form — selalu diisi dari sesi login. --}}
<form method="POST" action="{{ $log ? route('maintenance.update', $log) : route('maintenance.store') }}"
      class="grid max-w-2xl grid-cols-1 gap-4 sm:grid-cols-2"
>
    @csrf
    @if ($log)
        @method('PUT')
    @endif

    <div class="sm:col-span-2">
        <label for="machine_id" class="mb-1 block text-sm font-medium text-ink">Mesin</label>
        <select id="machine_id" name="machine_id" required
                class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('machine_id') border-danger @enderror"
        >
            <option value="">Pilih mesin</option>
            @foreach ($machines as $machine)
                <option value="{{ $machine->id }}" @selected((string) old('machine_id', $log?->machine_id) === (string) $machine->id)>{{ $machine->name }} ({{ $machine->serial_number }})</option>
            @endforeach
        </select>
        @error('machine_id') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="issue" class="mb-1 block text-sm font-medium text-ink">Masalah</label>
        <input id="issue" name="issue" type="text" required value="{{ old('issue', $log?->issue) }}" placeholder="Contoh: Tombol player 1 tidak merespons"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('issue') border-danger @enderror"
        >
        @error('issue') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="action_taken" class="mb-1 block text-sm font-medium text-ink">Tindakan yang Dilakukan <span class="font-normal text-ink-muted">(opsional)</span></label>
        <textarea id="action_taken" name="action_taken" rows="3" placeholder="Contoh: Ganti sakelar tombol dan uji ulang"
                  class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('action_taken') border-danger @enderror"
        >{{ old('action_taken', $log?->action_taken) }}</textarea>
        @error('action_taken') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="cost" class="mb-1 block text-sm font-medium text-ink">Biaya (Rp)</label>
        <input id="cost" name="cost" type="number" min="0" step="1000" required value="{{ old('cost', $log?->cost ?? 0) }}"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('cost') border-danger @enderror"
        >
        @error('cost') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="date" class="mb-1 block text-sm font-medium text-ink">Tanggal</label>
        <input id="date" name="date" type="date" required value="{{ old('date', $log?->date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('date') border-danger @enderror"
        >
        @error('date') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="status" class="mb-1 block text-sm font-medium text-ink">Status</label>
        <select id="status" name="status" required
                class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('status') border-danger @enderror"
        >
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $log?->status ?? \App\Models\MaintenanceLog::STATUS_OPEN) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3 sm:col-span-2">
        <button type="submit" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Simpan</button>
        <a href="{{ route('maintenance.index') }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Batal</a>
    </div>
</form>
