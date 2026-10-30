@php $record = $record ?? null; @endphp

{{-- Form bersama untuk tambah ($record kosong) dan ubah catatan pendapatan.
      user_id tidak pernah dikirim dari form — selalu diisi dari sesi login. --}}
<form method="POST" action="{{ $record ? route('income.update', $record) : route('income.store') }}"
      class="grid max-w-2xl grid-cols-1 gap-4 sm:grid-cols-2"
>
    @csrf
    @if ($record)
        @method('PUT')
    @endif

    <div class="sm:col-span-2">
        <label for="machine_id" class="mb-1 block text-sm font-medium text-ink">Mesin</label>
        <select id="machine_id" name="machine_id" required
                class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('machine_id') border-danger @enderror"
        >
            <option value="">Pilih mesin</option>
            @foreach ($machines as $machine)
                <option value="{{ $machine->id }}" @selected((string) old('machine_id', $record?->machine_id) === (string) $machine->id)>{{ $machine->name }} ({{ $machine->serial_number }})</option>
            @endforeach
        </select>
        @error('machine_id') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="date" class="mb-1 block text-sm font-medium text-ink">Tanggal</label>
        <input id="date" name="date" type="date" required value="{{ old('date', $record?->date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('date') border-danger @enderror"
        >
        @error('date') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="play_count" class="mb-1 block text-sm font-medium text-ink">Jumlah Permainan</label>
        <input id="play_count" name="play_count" type="number" min="1" step="1" required value="{{ old('play_count', $record?->play_count) }}" placeholder="Contoh: 120"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('play_count') border-danger @enderror"
        >
        @error('play_count') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="total_income" class="mb-1 block text-sm font-medium text-ink">Total Pendapatan (Rp)</label>
        <input id="total_income" name="total_income" type="number" min="0" step="1000" required value="{{ old('total_income', $record?->total_income) }}" placeholder="Contoh: 1920000"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('total_income') border-danger @enderror"
        >
        <p class="mt-1 text-xs text-ink-muted">Jumlah permainan x harga per main mesin.</p>
        @error('total_income') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3 sm:col-span-2">
        <button type="submit" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Simpan</button>
        <a href="{{ route('income.index') }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Batal</a>
    </div>
</form>
