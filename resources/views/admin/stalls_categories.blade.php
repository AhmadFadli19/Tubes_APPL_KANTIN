@extends('layouts.app', ['title' => 'Admin — Kelola Data Stan & Kategori'])

@section('content')
<div class="w-full min-h-screen bg-[#faf9fb] flex flex-col justify-between pb-24 md:pb-12">

    <!-- Top Navigation Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-gray-200/80 px-4 sm:px-8 py-3.5 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
            <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 sm:gap-3 hover:opacity-85 transition shrink-0" title="Kantin Tel-U">
                <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Tel-U" class="w-8 h-8 sm:w-10 sm:h-10 object-contain shrink-0 drop-shadow-2xs" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <span class="text-xs sm:text-sm font-black text-[#1a1c1d] tracking-tight uppercase hover:text-[#ed1e28] transition truncate">KELOLA STAN & KATEGORI</span>
                        <span class="bg-red-50 text-[#ed1e28] text-[9px] sm:text-[10px] font-bold px-1.5 sm:px-2 py-0.5 rounded-md shrink-0">MASTER DATA</span>
                    </div>
                    <div class="text-[10px] sm:text-[11px] text-[#5b5c61] truncate hidden xs:block">Pengaturan Area Kantin, Gerai Penjual, dan Jenis Menu</div>
                </div>
            </a>

            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <nav class="hidden md:flex items-center gap-1.5 lg:gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-[#5b5c61] hover:bg-gray-100 transition">Kelola Akun</a>
                    <a href="{{ route('admin.transactions') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-[#5b5c61] hover:bg-gray-100 transition">Pantau Transaksi</a>
                    <a href="{{ route('admin.stalls.categories') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#ed1e28] text-white shadow-2xs">Stan & Kategori</a>
                    <a href="{{ route('admin.reports') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-[#5b5c61] hover:bg-gray-100 transition">Laporan Penjualan</a>
                </nav>

                <!-- Admin Avatar Profile Button (Clicking opens Biodata & Akun) -->
                <a href="{{ route('profile.index') }}" class="flex items-center gap-2 px-2 py-1 sm:px-2.5 sm:py-1.5 bg-gray-50 hover:bg-red-50 border border-gray-200 hover:border-red-200 rounded-xl transition group shadow-2xs" title="Buka Biodata & Akun Admin">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full overflow-hidden bg-[#ed1e28] text-white font-black text-xs flex items-center justify-center shrink-0 border border-white shadow-2xs">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                        @else
                            <span>{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-black text-[#1a1c1d] group-hover:text-[#ed1e28] transition leading-tight">{{ auth()->user()->name ?? 'Super Admin' }}</div>
                        <div class="text-[9px] font-bold text-gray-400 uppercase">Biodata & Akun ›</div>
                    </div>
                </a>

                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Keluar dari panel admin?');">
                    @csrf
                    <button type="submit" class="p-1.5 sm:px-3 sm:py-1.5 bg-gray-100 hover:bg-red-50 text-[#1a1c1d] hover:text-red-600 text-xs font-bold rounded-xl transition flex items-center gap-1.5" title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-6 space-y-6 flex-1">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left: Stalls List & Management (8 cols) -->
            <div class="lg:col-span-8 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-[#1a1c1d]">Daftar Stan Kantin Kampus</h2>
                        <p class="text-xs text-[#5b5c61]">Data gerai dan penugasan pedagang mitra Tel-U</p>
                    </div>
                    <span class="text-xs font-bold text-[#ed1e28] bg-rose-50 px-3 py-1 rounded-full border border-red-200">
                        {{ $stalls->count() }} Stan Terdaftar
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($stalls as $stall)
                        <div class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-2xs space-y-3 relative group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 shrink-0">
                                        <img src="{{ asset($stall->image ?? 'assets/figma/6a55e.png') }}" alt="{{ $stall->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10px] font-bold text-[#ed1e28] uppercase">{{ $stall->code ?? 'STAN' }}</span>
                                        <h3 class="text-sm font-bold text-[#1a1c1d] truncate">{{ $stall->name }}</h3>
                                        <p class="text-xs text-[#5b5c61] truncate mt-0.5">{{ $stall->location }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0 {{ $stall->is_open ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $stall->is_open ? 'Buka' : 'Tutup' }}
                                </span>
                            </div>

                            <div class="bg-[#faf9fb] rounded-xl p-2.5 text-xs text-[#5b5c61] space-y-1">
                                <div class="flex justify-between">
                                    <span>Penjual / Pemilik:</span>
                                    <span class="font-bold text-[#1a1c1d]">{{ $stall->user->name ?? 'Belum Ditugaskan' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Jumlah Menu:</span>
                                    <span class="font-semibold text-[#1a1c1d]">{{ $stall->menus_count }} Menu</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-1 border-t border-gray-100">
                                <form method="POST" action="{{ route('admin.stalls.delete', $stall->id) }}">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Hapus data stan {{ $stall->name }}?')" class="text-xs text-red-600 hover:underline">
                                        Hapus Stan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Master Kategori Menu Makanan (4 cols) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Form & List Kategori Menu -->
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-2xs space-y-4">
                    <h3 class="text-sm font-bold text-[#1a1c1d]">Kategori Menu Makanan</h3>

                    <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-3">
                        @csrf
                        <div class="flex gap-2">
                            <input type="text" name="icon" placeholder="Icon (🍲)" class="w-20 bg-[#f6f6f7] text-xs rounded-xl h-10 px-3 border border-transparent focus:bg-white focus:border-[#ed1e28]">
                            <input type="text" name="name" required placeholder="Nama Kategori..." class="flex-1 bg-[#f6f6f7] text-xs rounded-xl h-10 px-3 border border-transparent focus:bg-white focus:border-[#ed1e28]">
                        </div>
                        <button type="submit" class="w-full bg-[#1a1c1d] hover:bg-black text-white font-bold text-xs h-9 rounded-xl transition">
                            + Tambah Kategori
                        </button>
                    </form>

                    <!-- Existing Categories -->
                    <div class="divide-y divide-gray-100 pt-2">
                        @foreach($categories as $cat)
                            <div class="py-2 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span>{{ $cat->icon ?? '🍽️' }}</span>
                                    <span class="font-semibold text-[#1a1c1d]">{{ $cat->name }}</span>
                                    <span class="text-[10px] text-gray-400">({{ $cat->menus_count }} menu)</span>
                                </div>
                                <form method="POST" action="{{ route('admin.categories.delete', $cat->id) }}">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Hapus kategori {{ $cat->name }}?')" class="text-gray-400 hover:text-red-600">
                                        ✕
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

    </main>

    <!-- Institutional Footer -->
    <footer class="bg-white border-t border-gray-100 py-4 px-6 text-center text-xs text-[#5b5c61]">
        Universitas Telkom • SMPEK Kantin Terpadu
    </footer>

    <!-- Admin Mobile Bottom Navigation Bar (md:hidden) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 px-4 py-2 shadow-lg flex items-center justify-around">
        <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('admin.dashboard') ? 'text-[#ed1e28]' : 'text-gray-500 hover:text-gray-900' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span class="text-[10px] {{ request()->routeIs('admin.dashboard') ? 'font-black' : 'font-bold' }}">Kelola Akun</span>
        </a>
        <a href="{{ route('admin.transactions') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('admin.transactions') ? 'text-[#ed1e28]' : 'text-gray-500 hover:text-gray-900' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('admin.transactions') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="text-[10px] {{ request()->routeIs('admin.transactions') ? 'font-black' : 'font-bold' }}">Transaksi</span>
        </a>
        <a href="{{ route('admin.stalls.categories') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('admin.stalls.categories') ? 'text-[#ed1e28]' : 'text-gray-500 hover:text-gray-900' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('admin.stalls.categories') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span class="text-[10px] {{ request()->routeIs('admin.stalls.categories') ? 'font-black' : 'font-bold' }}">Stan & Kat</span>
        </a>
        <a href="{{ route('admin.reports') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('admin.reports') ? 'text-[#ed1e28]' : 'text-gray-500 hover:text-gray-900' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('admin.reports') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="text-[10px] {{ request()->routeIs('admin.reports') ? 'font-black' : 'font-bold' }}">Laporan</span>
        </a>
        <a href="{{ route('profile.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('profile.*') ? 'text-[#ed1e28]' : 'text-gray-500 hover:text-gray-900' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('profile.*') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[10px] {{ request()->routeIs('profile.*') ? 'font-black' : 'font-bold' }}">Biodata</span>
        </a>
    </nav>

</div>
@endsection
