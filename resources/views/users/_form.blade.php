@php $user = $user ?? null; @endphp

{{-- Form bersama untuk tambah ($user kosong) dan ubah. Password opsional
     saat mengubah: kosongkan agar password lama tetap dipakai. --}}
<form method="POST" action="{{ $user ? route('users.update', $user) : route('users.store') }}"
      class="grid max-w-2xl grid-cols-1 gap-4 sm:grid-cols-2"
>
    @csrf
    @if ($user)
        @method('PUT')
    @endif

    <div>
        <label for="name" class="mb-1 block text-sm font-medium text-ink">Nama</label>
        <input id="name" name="name" type="text" required value="{{ old('name', $user?->name) }}"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('name') border-danger @enderror"
        >
        @error('name') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="mb-1 block text-sm font-medium text-ink">Email</label>
        <input id="email" name="email" type="email" required value="{{ old('email', $user?->email) }}"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('email') border-danger @enderror"
        >
        @error('email') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password" class="mb-1 block text-sm font-medium text-ink">Password</label>
        <input id="password" name="password" type="password" {{ $user ? '' : 'required' }}
               value=""
               placeholder="{{ $user ? 'Kosongkan agar tidak berubah' : 'Minimal 8 karakter' }}"
               autocomplete="new-password"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('password') border-danger @enderror"
        >
        @error('password') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password_confirmation" class="mb-1 block text-sm font-medium text-ink">Konfirmasi Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" value=""
               autocomplete="new-password"
               class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30"
        >
    </div>

    <div>
        <label for="role" class="mb-1 block text-sm font-medium text-ink">Role</label>
        <select id="role" name="role" required
                class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('role') border-danger @enderror"
        >
            @foreach ($roles as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user?->role ?? \App\Models\User::ROLE_STAFF) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('role') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3 sm:col-span-2">
        <button type="submit" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Simpan</button>
        <a href="{{ route('users.index') }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Batal</a>
    </div>
</form>
