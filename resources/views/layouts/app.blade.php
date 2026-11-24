<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Kantin Tel-U — Sistem Manajemen Pemesanan & Antrean Digital' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    
    <!-- Standalone Production Assets (100% PHP/HTML — Tanpa Perlu Node.js di Server Hosting) -->
    @php
        $manifestPath = public_path('build/manifest.json');
        $appCss = null;
        $appJs = null;
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $appCss = $manifest['resources/css/app.css']['file'] ?? null;
            $appJs = $manifest['resources/js/app.js']['file'] ?? null;
        }
    @endphp

    @if(!empty($appCss))
        <link rel="stylesheet" href="{{ asset('build/' . $appCss) }}">
    @endif
    @if(!empty($appJs))
        <script src="{{ asset('build/' . $appJs) }}" defer></script>
    @endif

    <!-- Tailwind CSS CDN (Memastikan seluruh styling UI tampil instan & rapi tanpa runtime Node.js) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'sans-serif'],
                    },
                    colors: {
                        'telkom-red': '#ed1e28',
                        'telkom-darkred': '#b6252a',
                        'telkom-maroon': '#bb0016',
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f3f4f6;
            -webkit-tap-highlight-color: transparent;
        }
        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#f0f2f5] text-[#1a1c1d] min-h-screen flex flex-col antialiased selection:bg-[#ed1e28] selection:text-white">

    <!-- Flash Notifications Toast -->
    <div id="toast-container" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-11/12 max-w-md pointer-events-none space-y-2">
        @if(session('success'))
            <div class="pointer-events-auto bg-[#1a1c1d] text-white px-4 py-3 rounded-2xl shadow-xl border border-gray-700/50 flex items-center gap-3 animate-bounce-short">
                <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="text-xs font-medium leading-relaxed">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="pointer-events-auto bg-[#1a1c1d] text-white px-4 py-3 rounded-2xl shadow-xl border border-red-900/50 flex items-center gap-3 animate-bounce-short">
                <div class="w-7 h-7 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div class="text-xs font-medium leading-relaxed">{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="pointer-events-auto bg-[#1a1c1d] text-white px-4 py-3 rounded-2xl shadow-xl border border-red-900/50 flex items-center gap-3 animate-bounce-short">
                <div class="w-7 h-7 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="text-xs font-medium leading-relaxed">
                    {{ $errors->first() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content Shell -->
    <main class="flex-1 w-full flex flex-col">
        @yield('content')
    </main>

    @stack('scripts')
    <script>
        // Auto-hide toast messages after 4 seconds
        setTimeout(() => {
            const toastContainer = document.getElementById('toast-container');
            if (toastContainer) {
                toastContainer.style.transition = 'opacity 0.5s ease-out';
                toastContainer.style.opacity = '0';
                setTimeout(() => toastContainer.remove(), 500);
            }
        }, 4000);

        // Universal Camera / Scroll Position Preservation across form submits and reloads
        (function() {
            const scrollKey = 'kantin_scroll_pos_' + window.location.pathname;

            // Before any form submits, store current scroll position
            document.addEventListener('submit', function() {
                sessionStorage.setItem(scrollKey, window.scrollY.toString());
            }, true);

            // Restore scroll position after reload / redirect if previously saved
            const savedScroll = sessionStorage.getItem(scrollKey);
            if (savedScroll !== null) {
                sessionStorage.removeItem(scrollKey);
                const pos = parseInt(savedScroll, 10);
                window.scrollTo({ top: pos, behavior: 'instant' });
                window.addEventListener('load', function() {
                    window.scrollTo({ top: pos, behavior: 'instant' });
                });
            }
        })();
    </script>
</body>
</html>
