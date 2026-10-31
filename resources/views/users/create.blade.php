@extends('layouts.app')

@section('title', 'Tambah Pengguna — Arcadia')

@section('content')
    <h1 class="text-2xl font-bold text-ink">Tambah Pengguna</h1>
    <p class="mt-1 text-sm text-ink-muted">Buat akun admin atau staff baru.</p>

    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        @include('users._form')
    </div>
@endsection
