@extends('layouts.app')

@section('title', 'Tambah Log Perawatan — Arcadia')

@section('content')
    <h1 class="text-2xl font-bold text-ink">Tambah Log Perawatan</h1>
    <p class="mt-1 text-sm text-ink-muted">Catat masalah mesin dan tindakan yang dilakukan.</p>

    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        @include('maintenance._form')
    </div>
@endsection
