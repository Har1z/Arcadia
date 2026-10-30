@extends('layouts.app')

@section('title', 'Tambah Pendapatan — Arcadia')

@section('content')
    <h1 class="text-2xl font-bold text-ink">Tambah Pendapatan</h1>
    <p class="mt-1 text-sm text-ink-muted">Catat pendapatan permainan harian untuk satu mesin.</p>

    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        @include('income._form')
    </div>
@endsection
