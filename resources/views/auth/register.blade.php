@extends('layouts.guest')

@section('title', 'Daftar — Arcadia')

@section('content')
    <h1 class="text-xl font-bold text-ink">Daftar Akun</h1>
    <p class="mt-1 text-sm text-ink-muted">Akun baru otomatis terdaftar sebagai staff.</p>

    <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="name" class="mb-1 block text-sm font-medium text-ink">Nama</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
            @error('name')
                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="mb-1 block text-sm font-medium text-ink">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                   class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
            @error('email')
                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium text-ink">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
            @error('password')
                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-ink">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink placeholder:text-ink-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
        </div>

        <button type="submit" class="mt-2 w-full rounded-ui bg-accent px-4 py-3 text-base font-semibold text-on-accent transition-colors hover:bg-accent-hover">
            Daftar
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-muted">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-medium text-accent hover:underline">Masuk di sini</a>
    </p>
@endsection
