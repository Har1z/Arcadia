@extends('layouts.app')

@section('title', 'Tambah Kategori — Arcadia')

@section('content')
    <h1 class="text-2xl font-bold text-ink">Tambah Kategori</h1>
    <p class="mt-1 text-sm text-ink-muted">Kelompokkan mesin berdasarkan jenis permainan.</p>

    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        @include('categories._form')
    </div>
@endsection
