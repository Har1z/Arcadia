@extends('layouts.app')

@section('title', 'Edit Log Perawatan — Arcadia')

@section('content')
    <h1 class="text-2xl font-bold text-ink">Edit Log Perawatan</h1>
    <p class="mt-1 text-sm text-ink-muted">{{ $log->machine->name }} — {{ $log->date->translatedFormat('d M Y') }}</p>

    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        @include('maintenance._form', ['log' => $log])
    </div>
@endsection
