@extends('layouts.app')

@section('title', 'Tambah Mesin — Arcadia')

@section('content')
    <h1 class="text-2xl font-bold text-ink">Tambah Mesin</h1>
    <p class="mt-1 text-sm text-ink-muted">Daftarkan mesin arcade baru ke dalam inventaris.</p>

    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        @include('machines._form')
    </div>
@endsection
