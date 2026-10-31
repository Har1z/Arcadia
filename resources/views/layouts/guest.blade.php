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
<body class="flex min-h-screen items-center justify-center bg-bg-soft p-4 font-sans text-ink antialiased">
    <div class="absolute right-4 top-4">
        @include('partials._theme-toggle')
    </div>

    <div class="w-full max-w-md rounded-ui border border-line bg-surface p-8 shadow-card">
        <div class="mb-8 text-center">
            <p class="text-3xl font-bold tracking-tight text-accent">Arcadia</p>
            <p class="mt-1 text-sm text-ink-muted">Sistem Manajemen Aset Mesin Arcade</p>
        </div>

        @yield('content')
    </div>
</body>
</html>
