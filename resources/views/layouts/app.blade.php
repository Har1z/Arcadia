<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/favicon.webp" type="image/webp">
    <title>@yield('title', 'Arcadia')</title>

    {{-- SECURITY: must run before the stylesheet so the saved theme is
         applied before first paint (no white/dark flash). --}}
    <script>
        (function () {
            var theme = localStorage.getItem('theme');
            if (theme !== 'light' && theme !== 'dark') {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.dataset.theme = theme;
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg-soft font-sans text-ink antialiased">
    <div class="min-h-screen lg:flex">

        {{-- Sidebar: fixed column on desktop, off-canvas drawer below 1024px. --}}
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-line bg-surface transition-transform duration-200 lg:translate-x-0"
        >
            <div class="flex h-16 items-center justify-between border-b border-line px-5">
                <a href="/dashboard" class="text-xl font-bold tracking-tight text-accent">Arcadia</a>
                <button type="button" data-drawer-close aria-label="Tutup menu"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-full text-ink-muted hover:bg-surface-alt lg:hidden"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto p-3" aria-label="Menu utama">
                {{--
                    SECURITY: the Users menu is only rendered for admins.
                    Server-side checks in controllers/middleware remain the
                    real protection; this only hides the link.
                --}}
                @foreach ([
                    '/dashboard' => ['Dashboard', 'M3 12h.01M3 12a9 9 0 1 1 18 0 9 9 0 0 1-18 0Zm3-6.5V11h2V5.5a9 9 0 0 0-2 0Zm10 0V11h2V5.5a9 9 0 0 0-2 0ZM5 11h14'],
                    '/machines' => ['Mesin', 'M6 3h12l1 7a7 7 0 0 1-14 0l1-7Zm0 0H4m4 7a2 2 0 1 0 4 0m-4 11h8m-4-4v4'],
                    '/categories' => ['Kategori', 'M7 7h.01M7 3h5.6a2 2 0 0 1 1.4.6l4.4 4.4a2 2 0 0 1 .6 1.4V17a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z'],
                    '/maintenance' => ['Perawatan', 'M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76Z'],
                    '/income' => ['Pendapatan', 'M12 2v20m5-16H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'],
                    '/users' => ['Pengguna', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m4-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm14 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75'],
                ] as $path => [$label, $icon])
                    @if ($path !== '/users' || (auth()->check() && auth()->user()->isAdmin()))
                        <a href="{{ $path }}"
                           class="flex items-center gap-3 rounded-ui px-3 py-2 text-sm font-medium {{ request()->is($path, $path.'/*') ? 'bg-accent-soft text-accent' : 'text-ink-muted hover:bg-surface-alt hover:text-ink' }}"
                           @if (request()->is($path, $path.'/*')) aria-current="page" @endif
                        >
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $icon }}"/></svg>
                            {{ $label }}
                        </a>
                    @endif
                @endforeach
            </nav>
        </aside>

        {{-- Click target that closes the mobile drawer. --}}
        <div id="sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-scrim lg:hidden" aria-hidden="true"></div>

        <div class="flex min-w-0 flex-1 flex-col lg:ml-64">
            <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-line bg-surface px-4 sm:px-6">
                <button type="button" data-drawer-open aria-label="Buka menu"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full text-ink-muted hover:bg-surface-alt hover:text-ink lg:hidden"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="ml-auto flex items-center gap-2 sm:gap-3">
                    @auth
                        <span class="hidden text-sm font-medium text-ink sm:inline">{{ auth()->user()->name }}</span>
                        {{-- SECURITY: role badge is display-only; real access
                             control lives in middleware and policies. --}}
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold uppercase tracking-wide {{ auth()->user()->isAdmin() ? 'bg-accent text-on-accent' : 'bg-accent-soft text-accent' }}">
                            {{ auth()->user()->role }}
                        </span>
                    @else
                        <span class="text-sm text-ink-muted">Tamu</span>
                    @endauth

                    @include('partials._theme-toggle')

                    {{-- SECURITY: logout always goes through POST (not GET)
                         so it can't be triggered by a crafted link. --}}
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="rounded-ui px-3 py-2 text-sm font-medium text-ink-muted transition-colors hover:bg-danger/10 hover:text-danger">
                            Keluar
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6">
                @include('partials._flash')
                @yield('content')
            </main>
        </div>
    </div>

    @include('partials._confirm-delete')
</body>
</html>
