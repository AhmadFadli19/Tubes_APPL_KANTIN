<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - Kantin Tel-U</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
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
</head>
<body class="bg-[#faf9fb] min-h-screen flex items-center justify-center p-4 font-['Plus_Jakarta_Sans',sans-serif]">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
        <div class="w-16 h-16 bg-red-50 text-[#ed1e28] rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-[#1a1c1d] mb-2">Akses Ditolak</h1>
        <p class="text-[#5b5c61] text-sm mb-6 leading-relaxed">
            {{ $message ?? 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses halaman ini.' }}
        </p>
        <div class="space-y-3">
            @auth
                @if(auth()->user()->role === 'pembeli')
                    <a href="{{ route('menu.index') }}" class="block w-full py-3 px-4 bg-[#ed1e28] text-white font-semibold rounded-xl hover:bg-[#bb0016] transition text-sm">Kembali ke Katalog Menu</a>
                @elseif(auth()->user()->role === 'penjual')
                    <a href="{{ route('merchant.dashboard') }}" class="block w-full py-3 px-4 bg-[#ed1e28] text-white font-semibold rounded-xl hover:bg-[#bb0016] transition text-sm">Kembali ke Manajemen Stan</a>
                @elseif(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="block w-full py-3 px-4 bg-[#ed1e28] text-white font-semibold rounded-xl hover:bg-[#bb0016] transition text-sm">Kembali ke Dasbor Admin</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="block w-full py-3 px-4 bg-[#ed1e28] text-white font-semibold rounded-xl hover:bg-[#bb0016] transition text-sm">Masuk ke Akun</a>
            @endauth
            <a href="javascript:history.back()" class="block w-full py-2.5 px-4 bg-gray-100 text-[#5b5c61] font-medium rounded-xl hover:bg-gray-200 transition text-sm">Halaman Sebelumnya</a>
        </div>
    </div>
</body>
</html>
