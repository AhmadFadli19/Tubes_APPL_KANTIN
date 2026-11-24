@extends('layouts.app')

@php
    $currentRoute = request()->route()->getName();
    $activeOrdersCount = \App\Models\Order::where('stall_id', $stall->id ?? (auth()->user()->stall->id ?? 0))
        ->whereIn('status', ['menunggu', 'diproses', 'siap_diambil'])
        ->count();
@endphp

@section('content')
<div class="min-h-screen bg-[#faf9fb] flex flex-col antialiased">

    <!-- DESKTOP FIXED SIDEBAR (Matching Reference Screenshots 4, 5, 6) -->
    <aside class="hidden lg:flex w-64 bg-white border-r border-gray-100 flex-col justify-between fixed top-0 bottom-0 left-0 z-30 p-5 select-none shadow-xs">
        <div class="space-y-6">
            
            <!-- Brand / Logo Hub -->
            <div class="flex items-center gap-3 px-2">
                <a href="{{ route('home') }}" class="shrink-0" title="Kantin Tel-U">
                    <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Logo Kantin Tel-U" class="w-10 h-10 object-contain drop-shadow-2xs" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                </a>
                <div>
                    <h2 class="text-sm font-black text-[#1a1c1d] tracking-tight leading-tight">Kantin Tel-U</h2>
                    <p class="text-[10px] font-bold text-gray-400 tracking-wider uppercase">Merchant & Admin Hub</p>
                </div>
            </div>

            <!-- Stall Quick Status Pill -->
            <div class="bg-gray-50/80 rounded-2xl p-2.5 border border-gray-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ ($stall->is_open ?? true) ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                    <span class="font-bold text-gray-700 text-[11px]">{{ ($stall->is_open ?? true) ? 'Stan Buka' : 'Stan Tutup' }}</span>
                </div>
                <span class="bg-white px-2 py-0.5 rounded-lg border border-gray-200 text-[10px] font-bold text-gray-500">
                    {{ $stall->code ?? 'Gedung C' }}
                </span>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1.5 pt-1">
                
                <!-- 1. Papan Antrean & Pesanan -->
                <a href="{{ route('merchant.dashboard') }}" 
                   class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-bold transition {{ str_starts_with($currentRoute, 'merchant.dashboard') ? 'bg-[#ed1e28] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <span class="text-base">📋</span>
                        <span>Papan Antrean & Pesanan</span>
                    </div>
                    @if($activeOrdersCount > 0)
                        <span class="px-2 py-0.5 text-[10px] font-black rounded-full {{ str_starts_with($currentRoute, 'merchant.dashboard') ? 'bg-white text-[#ed1e28]' : 'bg-red-100 text-[#ed1e28]' }}">
                            {{ $activeOrdersCount }}
                        </span>
                    @endif
                </a>

                <!-- 2. Menu & Stok Makanan -->
                <a href="{{ route('merchant.menus.index') }}" 
                   class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-bold transition {{ str_starts_with($currentRoute, 'merchant.menus') ? 'bg-[#ed1e28] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <span class="text-base">🍽️</span>
                        <span>Menu & Stok Makanan</span>
                    </div>
                </a>

                <!-- 3. Laporan & Keuangan -->
                <a href="{{ route('merchant.reports') }}" 
                   class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-bold transition {{ str_starts_with($currentRoute, 'merchant.reports') ? 'bg-[#ed1e28] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <span class="text-base">📊</span>
                        <span>Laporan & Keuangan</span>
                    </div>
                </a>

                <!-- 4. Pengaturan Stan & Akun -->
                <a href="{{ route('merchant.stall.profile') }}" 
                   class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-bold transition {{ str_starts_with($currentRoute, 'merchant.stall') ? 'bg-[#ed1e28] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <span class="text-base">⚙️</span>
                        <span>Pengaturan Stan & Akun</span>
                    </div>
                </a>

            </nav>
        </div>

        <!-- Bottom Sidebar Info & Logout -->
        <div class="space-y-3 pt-4 border-t border-gray-100">
            <div class="bg-gray-50 rounded-2xl p-3 border border-gray-100 text-[11px] text-gray-500 leading-relaxed">
                <div class="font-bold text-[#1a1c1d] flex items-center gap-1.5 mb-1">
                    <span>💡</span>
                    <span>Bantuan Admin Tel-U</span>
                </div>
                Pusat operasional kantin dan support QRIS Telkom.
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-gray-500 hover:text-red-600 hover:bg-red-50 transition">
                    <span>🚪</span>
                    <span>Keluar Akun</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN PAGE WRAPPER (Desktop offsets for sidebar) -->
    <div class="lg:pl-64 flex-1 flex flex-col min-h-screen">
        
        <!-- DESKTOP TOP HEADER BAR -->
        <header class="hidden lg:flex h-16 bg-white border-b border-gray-100 px-8 items-center justify-between sticky top-0 z-20 shadow-2xs">
            
            <!-- Left: Operational Schedule Info -->
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-100">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Operasional: {{ $stall->opening_hours ?? '07:30 - 17:00 WIB' }}</span>
                </span>
            </div>

            <!-- Center: Search Input Bar -->
            <div class="w-96 max-w-md">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 text-xs">
                        🔍
                    </span>
                    <input type="text" 
                           id="globalDesktopSearch" 
                           placeholder="Cari nomor antrean, menu, nota..." 
                           class="w-full pl-9 pr-4 py-2 bg-gray-50/80 border border-gray-200 rounded-xl text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                </div>
            </div>

            <!-- Right: Notifications & Merchant Identity -->
            <div class="flex items-center gap-4">
                <button type="button" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-500 flex items-center justify-center relative border border-gray-200 transition" title="Notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#ed1e28] rounded-full border border-white"></span>
                </button>

                <div class="flex items-center gap-2.5 pl-2">
                    <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 group" title="Lihat Biodata Akun">
                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-red-100 border border-gray-200 shrink-0">
                            <img src="{{ asset(auth()->user()->avatar ?? 'assets/figma/6a55e.png') }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                        </div>
                        <div class="text-left hidden xl:block">
                            <div class="text-xs font-black text-[#1a1c1d] group-hover:text-[#ed1e28] transition truncate max-w-[130px]">{{ $stall->name ?? auth()->user()->name }}</div>
                            <div class="text-[10px] font-bold text-gray-400">Mitra Tel-U • Biodata Akun</div>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Keluar dari akun penjual?');" class="inline">
                        @csrf
                        <button type="submit" class="px-2.5 py-1.5 bg-gray-100 hover:bg-red-50 text-gray-500 hover:text-red-600 rounded-xl text-xs font-bold transition flex items-center gap-1" title="Keluar">
                            <span>🚪</span>
                            <span class="hidden 2xl:inline">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- MOBILE TOP APP HEADER (Matching Reference Screenshots 1, 2, 3) -->
        <header class="lg:hidden bg-white border-b border-gray-100 px-4 py-3 sticky top-0 z-30 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5 min-w-0">
                <a href="{{ route('home') }}" class="shrink-0" title="Kantin Tel-U">
                    <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Logo" class="w-8 h-8 sm:w-9 sm:h-9 object-contain drop-shadow-2xs shrink-0" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                </a>
                <div class="min-w-0">
                    <h1 class="text-xs sm:text-sm font-black text-[#1a1c1d] truncate">@yield('merchant_title', $stall->name ?? 'Kantin Tel-U')</h1>
                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-600 truncate">
                        <span class="w-1.5 h-1.5 rounded-full {{ ($stall->is_open ?? true) ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                        <span class="truncate uppercase">{{ ($stall->is_open ?? true) ? 'Stan Buka' : 'Stan Tutup' }} • {{ $stall->code ?? 'KANTIN TULT' }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Profile Link & Mobile Logout Button -->
            <div class="flex items-center gap-2">
                <a href="{{ route('profile.index') }}" class="w-8 h-8 rounded-full overflow-hidden bg-red-50 border border-gray-200 flex items-center justify-center shrink-0" title="Biodata Akun">
                    <img src="{{ asset(auth()->user()->avatar ?? 'assets/figma/6a55e.png') }}" alt="Profil" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                </a>
                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Keluar dari akun penjual?');" class="inline">
                    @csrf
                    <button type="submit" class="p-1.5 bg-gray-100 hover:bg-red-50 text-gray-500 hover:text-red-600 rounded-xl text-xs font-bold transition flex items-center" title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- PAGE MAIN CONTENT INJECTION -->
        <main class="flex-1 w-full pb-24 lg:pb-10">
            @yield('merchant_content')
        </main>

    </div>

    <!-- MOBILE FIXED BOTTOM NAVIGATION BAR (Matching Reference Screenshots 1, 2, 3) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-gray-200/90 px-3 py-1.5 z-40 flex items-center justify-around shadow-lg">
        
        <!-- 1. Pesanan (Papan Antrean) -->
        <a href="{{ route('merchant.dashboard') }}" 
           class="flex flex-col items-center py-1 px-2.5 rounded-xl transition relative {{ str_starts_with($currentRoute, 'merchant.dashboard') ? 'text-[#ed1e28]' : 'text-gray-400 hover:text-gray-600' }}">
            <div class="relative">
                <svg class="w-5 h-5 {{ str_starts_with($currentRoute, 'merchant.dashboard') ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                @if($activeOrdersCount > 0)
                    <span class="absolute -top-1.5 -right-2 bg-[#ed1e28] text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center border border-white">
                        {{ $activeOrdersCount }}
                    </span>
                @endif
            </div>
            <span class="text-[10px] font-bold mt-0.5">Pesanan</span>
        </a>

        <!-- 2. Menu & Stok -->
        <a href="{{ route('merchant.menus.index') }}" 
           class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ str_starts_with($currentRoute, 'merchant.menus') ? 'text-[#ed1e28]' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5 {{ str_starts_with($currentRoute, 'merchant.menus') ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span class="text-[10px] font-bold mt-0.5">Menu & Stok</span>
        </a>

        <!-- 3. Pendapatan (Reports) -->
        <a href="{{ route('merchant.reports') }}" 
           class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ str_starts_with($currentRoute, 'merchant.reports') ? 'text-[#ed1e28]' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5 {{ str_starts_with($currentRoute, 'merchant.reports') ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="text-[10px] font-bold mt-0.5">Pendapatan</span>
        </a>

        <!-- 4. Profil Stan -->
        <a href="{{ route('merchant.stall.profile') }}" 
           class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ str_starts_with($currentRoute, 'merchant.stall') ? 'text-[#ed1e28]' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5 {{ str_starts_with($currentRoute, 'merchant.stall') ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span class="text-[10px] font-bold mt-0.5">Profil Stan</span>
        </a>

        <!-- 5. Biodata Akun -->
        <a href="{{ route('profile.index') }}" 
           class="flex flex-col items-center py-1 px-2.5 rounded-xl transition {{ str_starts_with($currentRoute, 'profile') ? 'text-[#ed1e28]' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5 {{ str_starts_with($currentRoute, 'profile') ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[10px] font-bold mt-0.5">Biodata</span>
        </a>

    </nav>

</div>
@endsection
