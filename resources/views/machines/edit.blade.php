@extends('layouts.app')

@section('title', 'Edit Mesin — Arcadia')

@section('content')
    <h1 class="text-2xl font-bold text-ink">Edit Mesin</h1>
    <p class="mt-1 text-sm text-ink-muted">{{ $machine->name }} — {{ $machine->serial_number }}</p>

    <div class="mt-6 rounded-ui border border-line bg-surface p-5 shadow-card">
        {{-- SECURITY: staff may only change the status field; the full
             form is reserved for admins. The request validation enforces
             this server-side as well (other fields are 'prohibited'). --}}
        @if (auth()->user()->isAdmin())
            @include('machines._form', ['machine' => $machine])
        @else
            <form method="POST" action="{{ route('machines.update', $machine) }}" class="max-w-xs">
                @csrf
                @method('PUT')

                <label for="status" class="mb-1 block text-sm font-medium text-ink">Status</label>
                <select id="status" name="status" required
                        class="w-full rounded-ui border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 @error('status') border-danger @enderror"
                >
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $machine->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status') <p class="mt-1 text-sm text-danger" role="alert">{{ $message }}</p> @enderror

                <div class="mt-4 flex items-center gap-3">
                    <button type="submit" class="rounded-ui bg-accent px-4 py-2 text-sm font-medium text-on-accent hover:bg-accent-hover">Simpan Status</button>
                    <a href="{{ route('machines.index') }}" class="rounded-ui border border-line bg-surface px-4 py-2 text-sm font-medium text-ink hover:bg-surface-alt">Batal</a>
                </div>
            </form>
        @endif
    </div>
@endsection
