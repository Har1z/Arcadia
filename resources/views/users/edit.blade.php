@extends('layouts.app')

@section('title', 'Edit Pengguna — Arcadia')

@section('content')
    <h1 class="text-2xl font-bold text-ink">Edit Pengguna</h1>
    <p class="mt-1 text-sm text-ink-muted">{{ $user->name }} — {{ $user->email }}</p>

    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        @include('users._form', ['user' => $user])
    </div>
@endsection
