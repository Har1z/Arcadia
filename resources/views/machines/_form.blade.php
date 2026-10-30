@php $machine = $machine ?? null; @endphp

{{-- Form bersama untuk tambah ($machine kosong) dan ubah (admin saja).
     Nilai lama dipertahankan lewat old(), difallback ke data mesin. --}}
<form method="POST" action="{{ $machine ? route('machines.update', $machine) : route('machines.store') }}"
      class="grid max-w-2xl grid-cols-1 gap-4 sm:grid-cols-2"
>
    @csrf
    @if ($machine)
        @method('PUT')
    @endif

    <div class="sm:col-span-2">
        <label for="category_id" class="mb-1 block text-sm font-medium text-ink">Kategori</label>
        <select id="category_id" name="category_id" required
                class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('category_id') border-danger @enderror"
        >
            <option value="">Pilih kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $machine?->category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="name" class="mb-1 block text-sm font-medium text-ink">Nama Mesin</label>
        <input id="name" name="name" type="text" required value="{{ old('name', $machine?->name) }}"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('name') border-danger @enderror"
        >
        @error('name') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="serial_number" class="mb-1 block text-sm font-medium text-ink">Nomor Seri</label>
        <input id="serial_number" name="serial_number" type="text" required value="{{ old('serial_number', $machine?->serial_number) }}" placeholder="ARC-0001"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('serial_number') border-danger @enderror"
        >
        @error('serial_number') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="location" class="mb-1 block text-sm font-medium text-ink">Lokasi</label>
        <input id="location" name="location" type="text" required value="{{ old('location', $machine?->location) }}" placeholder="Lantai 1 - Zona A"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('location') border-danger @enderror"
        >
        @error('location') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="purchase_date" class="mb-1 block text-sm font-medium text-ink">Tanggal Pembelian</label>
        <input id="purchase_date" name="purchase_date" type="date" required value="{{ old('purchase_date', $machine?->purchase_date?->format('Y-m-d')) }}"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('purchase_date') border-danger @enderror"
        >
        @error('purchase_date') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="purchase_price" class="mb-1 block text-sm font-medium text-ink">Harga Beli (Rp)</label>
        <input id="purchase_price" name="purchase_price" type="number" min="0" step="1000" required value="{{ old('purchase_price', $machine?->purchase_price) }}"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('purchase_price') border-danger @enderror"
        >
        @error('purchase_price') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="price_per_play" class="mb-1 block text-sm font-medium text-ink">Harga per Main (Rp)</label>
        <input id="price_per_play" name="price_per_play" type="number" min="0" step="500" required value="{{ old('price_per_play', $machine?->price_per_play) }}"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('price_per_play') border-danger @enderror"
        >
        @error('price_per_play') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="mb-1 block text-sm font-medium text-ink">Status</label>
        <select id="status" name="status" required
                class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('status') border-danger @enderror"
        >
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $machine?->status ?? \App\Models\Machine::STATUS_ACTIVE) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3 sm:col-span-2">
        <button type="submit" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Simpan</button>
        <a href="{{ route('machines.index') }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Batal</a>
    </div>
</form>
