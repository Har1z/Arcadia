{{-- Navigasi halaman sederhana: sebelumnya, jendela 5 nomor, berikutnya.
     Dipakai via @include('partials._pagination', ['paginator' => $machines]). --}}
{{-- $paginator is passed in by the including view. --}}

@if ($paginator->hasPages())
    <nav class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm text-ink-muted" aria-label="Navigasi halaman">
        <p>Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data</p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="rounded-ui border border-line px-3 py-1.5 opacity-50" aria-hidden="true">&laquo; Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-ui border border-line bg-surface px-3 py-1.5 hover:bg-surface-alt">&laquo; Sebelumnya</a>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if ($page === $paginator->currentPage())
                    <span aria-current="page" class="rounded-ui bg-accent px-3 py-1.5 font-medium text-on-accent">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="rounded-ui border border-line bg-surface px-3 py-1.5 hover:bg-surface-alt">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-ui border border-line bg-surface px-3 py-1.5 hover:bg-surface-alt">Berikutnya &raquo;</a>
            @else
                <span class="rounded-ui border border-line px-3 py-1.5 opacity-50" aria-hidden="true">Berikutnya &raquo;</span>
            @endif
        </div>
    </nav>
@endif
