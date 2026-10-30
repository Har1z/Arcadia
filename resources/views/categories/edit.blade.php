@extends('layouts.app')

@section('title', 'Edit Kategori — Arcadia')

@section('content')
    <h1 class="text-2xl font-bold text-ink">Edit Kategori</h1>
    <p class="mt-1 text-sm text-ink-muted">{{ $category->name }}</p>

    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        @include('categories._form', ['category' => $category])
    </div>
@endsection
