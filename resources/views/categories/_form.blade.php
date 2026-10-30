@php $category = $category ?? null; @endphp

{{-- Form bersama untuk tambah dan ubah kategori (admin saja). --}}
<form method="POST" action="{{ $category ? route('categories.update', $category) : route('categories.store') }}" class="max-w-xl space-y-4">
    @csrf
    @if ($category)
        @method('PUT')
    @endif

    <div>
        <label for="name" class="mb-1 block text-sm font-medium text-ink">Nama Kategori</label>
        <input id="name" name="name" type="text" required value="{{ old('name', $category?->name) }}" placeholder="Racing"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('name') border-danger @enderror"
        >
        @error('name') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="mb-1 block text-sm font-medium text-ink">Deskripsi <span class="font-normal text-ink-muted">(opsional)</span></label>
        <textarea id="description" name="description" rows="3" placeholder="Mesin dengan tema game balapan."
                  class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('description') border-danger @enderror"
        >{{ old('description', $category?->description) }}</textarea>
        @error('description') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Simpan</button>
        <a href="{{ route('categories.index') }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Batal</a>
    </div>
</form>
