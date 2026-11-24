@extends('layouts.app', ['title' => 'Kantin Tel-U — Sistem Manajemen Pemesanan & Antrean Digital'])

@section('content')
<div class="w-full min-h-screen bg-[#faf9fb] flex flex-col items-center justify-between">

    <!-- Top Content Area -->
    <div class="w-full max-w-md mx-auto px-5 pt-4 pb-6 flex flex-col items-center text-center space-y-4">
        
        <!-- Red Circular Logo Floating at Top -->
        <div class="w-14 h-14 rounded-full bg-white p-2 shadow-lg border border-red-100 flex items-center justify-center shrink-0">
            <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Logo Tel-U" class="w-full h-full object-contain">
        </div>

        <!-- Hero University Canteen Illustration Card -->
        <div class="w-full h-56 sm:h-64 rounded-3xl overflow-hidden shadow-md border border-gray-100 relative bg-rose-50">
            <img src="{{ asset('assets/figma/7e0d3.png') }}" alt="Kantin Telkom University" class="w-full h-full object-cover">
            
            <!-- Open Stalls Pill Overlay -->
            <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-3 py-1 rounded-full text-[11px] font-bold text-gray-800 shadow-sm flex items-center gap-1.5 border border-white/60">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>32 Stan Buka Sekarang</span>
            </div>
        </div>

        <!-- Headings & Tagline -->
        <div class="space-y-1.5 pt-1">
            <h1 class="text-2xl sm:text-3xl font-black text-[#1a1c1d] tracking-tight leading-tight">
                Makan di Kantin<br>
                <span class="text-[#ed1e28]">Tanpa Antre Panjang</span>
            </h1>
            <p class="text-xs sm:text-sm text-[#5b5c61] leading-relaxed max-w-xs mx-auto">
                Pesan dari stan favoritmu di Tel-U, bayar via QRIS, dan ambil saat pesanan siap!
            </p>
        </div>

        <!-- Feature Pill Badges -->
        <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-[11px] font-bold">
                <span>🍱</span>
                <span>Ambil di Tempat</span>
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-[11px] font-bold">
                <span>⚡</span>
                <span>Bayar Instan QRIS</span>
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-[11px] font-bold">
                <span>🎟</span>
                <span>Antrean Real-time</span>
            </span>
        </div>

    </div>

    <!-- Bottom Curved Red Action Sheet -->
    <div class="w-full bg-[#ed1e28] text-white pt-8 pb-10 px-6 rounded-t-[2.5rem] shadow-2xl relative overflow-hidden">
        <!-- Subtle Glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-12 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

        <div class="max-w-md mx-auto space-y-3 relative z-10 text-center">
            
            <!-- Masuk Button (Dark Gray) -->
            <a href="{{ route('login') }}" class="w-full bg-[#3d4148] hover:bg-[#2e3137] text-white font-bold text-sm h-12 rounded-2xl shadow-md flex items-center justify-center gap-2.5 transition active:scale-95">
                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                <span>Masuk</span>
            </a>

            <!-- Daftar Akun Baru Button (White) -->
            <a href="{{ route('register') }}" class="w-full bg-white hover:bg-gray-100 text-[#ed1e28] font-bold text-sm h-12 rounded-2xl shadow-md flex items-center justify-center gap-2 transition active:scale-95">
                <svg class="w-5 h-5 text-[#ed1e28]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Daftar Akun Baru</span>
            </a>

            <!-- Lihat Stan & Menu Tanpa Login Link -->
            <div class="pt-2">
                <a href="{{ route('menu.index') }}" class="text-xs font-semibold text-white/90 hover:text-white inline-flex items-center gap-1.5 transition">
                    <span>Lihat stan & menu tanpa login</span>
                    <span>→</span>
                </a>
            </div>

            <!-- Footer Stamp -->
            <div class="pt-3 text-[10px] tracking-wider uppercase text-white/60 font-semibold">
                UNIVERSITAS TELKOM • SMPEK KANTIN TERPADU
            </div>

        </div>
    </div>

</div>
@endsection
