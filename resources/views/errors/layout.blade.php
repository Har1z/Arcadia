<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

    @vite('resources/css/app.css')
</head>
{{-- Standalone shell (no auth/session dependencies) so every error page,
     including 500, still renders in the coffee theme. --}}
<body class="flex min-h-screen items-center justify-center bg-bg-soft p-4 font-sans text-ink antialiased">
    <div class="absolute right-4 top-4">
        @include('partials._theme-toggle')
    </div>

    <div class="w-full max-w-md rounded-ui border border-line bg-surface p-8 text-center shadow-card">
        <p class="text-5xl font-bold tracking-tight text-accent">@yield('code')</p>
        <h1 class="mt-4 text-xl font-semibold text-ink">@yield('heading')</h1>
        <p class="mt-2 text-sm text-ink-muted">@yield('message')</p>

        <a href="{{ url('/') }}"
           class="mt-6 inline-flex items-center justify-center rounded-ui bg-accent px-4 py-2 text-sm font-semibold text-on-accent transition-colors hover:bg-accent-hover focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
            Kembali ke Beranda
        </a>
    </div>

    @vite('resources/js/app.js')
</body>
</html>
