@extends('layouts.app')

@section('title', 'Edit Catatan Pendapatan — Arcadia')

@section('content')
    <h1 class="text-2xl font-bold text-ink">Edit Catatan Pendapatan</h1>
    <p class="mt-1 text-sm text-ink-muted">{{ $record->machine->name }} — {{ $record->date->translatedFormat('d M Y') }}</p>

    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        @include('income._form', ['record' => $record])
    </div>
@endsection
