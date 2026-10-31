{{-- Loading overlay, shown by app.js while a same-origin navigation is
     pending (the remote database makes round-trips visibly slow).

     A plain maimai DX judgment ring: white notes spawn in the center and
     fly out along 8 lanes at 22.5° offsets (lane 1 top-right, clockwise),
     dissolving as they approach the ring, 1 -> 8 in order so the sweep
     reads as a continuous clockwise spin. The page behind is dimmed. --}}
<div id="loading-overlay"
     role="status"
     aria-label="Memuat"
     class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50"
>
    <div class="maimai-loader">
        <div class="maimai-ring"></div>
        @php
            // All lanes share one 2s cycle; spawns are spaced evenly across it.
            $cycle = 2.0;
            $step = $cycle / 8;
        @endphp
        @for ($i = 1; $i <= 8; $i++)
            @php
                // Arcade layout: lane i at 22.5° + (i-1)*45° from vertical.
                $angle = 22.5 + ($i - 1) * 45;
                // Negative delays start every element mid-cycle (no visible
                // restart). Note i must spawn i steps after note 1, so its
                // delay grows with i; that keeps the order 1 -> 8 clockwise.
                $noteDelay = ($i - 1) * $step - $cycle;
            @endphp
            <div class="maimai-lane" style="--angle: {{ $angle }}deg">
                <div class="maimai-note" style="--note-delay: {{ round($noteDelay, 3) }}s"></div>
            </div>
        @endfor
    </div>
    <span class="sr-only">Memuat</span>
</div>
