@extends('layouts.app', ['title' => 'Kantin Tel-U — Menu & Stan Kantin'])

@section('content')
<div class="w-full min-h-screen bg-[#faf9fb] flex flex-col justify-between pb-24 md:pb-12">

    <!-- Top Navigation Header (Matching Screenshot 1) -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-2xs px-4 sm:px-8 py-3">
        <div class="max-w-xl mx-auto flex items-center justify-between gap-3">
            
            <!-- Left: Red Circular Cutlery Icon & Location Selector -->
            <div class="flex items-center gap-2.5 min-w-0">
                <a href="{{ route('home') }}" class="shrink-0" title="Kantin Tel-U">
                    <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Logo Kantin Tel-U" class="w-9 h-9 sm:w-10 sm:h-10 object-contain drop-shadow-2xs" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-1 text-[10px] font-black tracking-wider text-[#ed1e28] uppercase">
                        <span>AMBIL DI</span>
                        <svg class="w-3 h-3 text-[#ed1e28]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-[#1a1c1d] truncate">
                        Kantin Gedung B / FEB Telkom Univ
                    </div>
                </div>
            </div>

            <!-- Right: Notification Bell & User Avatar -->
            <div class="flex items-center gap-2.5 shrink-0">
                
                <!-- Notification Bell -->
                <a href="{{ auth()->check() ? route('orders.index') : route('login') }}" class="w-9 h-9 rounded-full bg-gray-50 hover:bg-gray-100 border border-gray-200/80 flex items-center justify-center text-gray-700 relative transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#ed1e28] ring-2 ring-white"></span>
                </a>

                @auth
                    <!-- User Avatar Photo / Initial -->
                    <a href="{{ route('profile.index') }}" class="w-9 h-9 rounded-full overflow-hidden bg-gray-200 border-2 border-white shadow-xs flex items-center justify-center hover:opacity-90 transition" title="Biodata Akun">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                        @else
                            <span class="text-xs font-black text-gray-700">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        @endif
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1.5 bg-[#ed1e28] text-white rounded-xl text-xs font-bold shadow-xs">
                        Masuk
                    </a>
                @endauth

            </div>

        </div>
    </header>

    <!-- Main Content Area -->
    <main class="max-w-xl mx-auto w-full px-4 pt-3 space-y-4 flex-1">

        <!-- Greeting Row -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-base sm:text-lg font-black text-[#1a1c1d]">
                    Halo, {{ auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Sivitas Tel-U' }}! 👋
                </h1>
                <div class="text-[11px] text-[#5b5c61] flex items-center gap-1 mt-0.5">
                    <svg class="w-3 h-3 text-[#ed1e28]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <span>Kantin Terpadu Gd. Tokong Nanas (GKU)</span>
                </div>
            </div>

            <!-- Active Orders / Stalls Badge -->
            <div>
                @if($activeOrder)
                    <a href="{{ route('orders.show', $activeOrder->id) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-[11px] font-bold text-emerald-700 border border-emerald-200 shadow-2xs animate-pulse">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>1 Aktif</span>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-[11px] font-bold text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>{{ $stalls->where('is_open', true)->count() }} Aktif</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Search Bar with Live Recommendations & Filter Button -->
        <div class="relative" id="searchWrapper">
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <form id="liveSearchForm" method="GET" action="{{ route('menu.index') }}">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if(request('stall'))
                            <input type="hidden" name="stall" value="{{ request('stall') }}">
                        @endif
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input 
                                type="text" 
                                id="liveSearchInput"
                                name="q" 
                                value="{{ $search }}" 
                                autocomplete="off"
                                oninput="handleSearchAutocomplete(this.value)"
                                onfocus="handleSearchAutocomplete(this.value)"
                                placeholder="Cari nasi geprek, mie ayam, kopi..." 
                                class="w-full bg-[#f4f4f6] text-[#1a1c1d] placeholder-gray-400 text-xs rounded-2xl h-11 pl-10 pr-9 border border-transparent focus:border-[#ed1e28]/40 focus:bg-white focus:outline-none transition shadow-2xs"
                            >
                            <button type="button" id="clearSearchBtn" onclick="clearLiveSearch()" class="{{ $search ? '' : 'hidden' }} absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 text-xs font-bold">
                                ✕
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Filter Button (Opens Filter Modal) -->
                <button type="button" onclick="openFilterModal()" class="w-11 h-11 bg-[#ed1e28] hover:bg-[#d41720] text-white rounded-2xl flex items-center justify-center shrink-0 shadow-sm active:scale-95 transition" title="Filter Pencarian">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                </button>
            </div>

            <!-- Instant Search Recommendations Floating Dropdown -->
            <div id="searchSuggestionsDropdown" class="hidden absolute top-full left-0 right-0 mt-1.5 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden divide-y divide-gray-100 max-h-80 overflow-y-auto animate-in fade-in duration-150">
                <!-- Dynamically populated via JavaScript -->
            </div>
        </div>

        <!-- Active Filter Indicator Pill (if filtered by Stall or Search) -->
        @if($stallId || $search || ($categorySlug && $categorySlug !== 'semua'))
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-0.5">
                <span class="text-[10px] text-gray-400 font-bold uppercase">Filter:</span>
                @if($stallId)
                    @php $activeStall = $stalls->firstWhere('id', (int)$stallId); @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-red-50 text-[#ed1e28] border border-red-200 text-[10px] font-bold shrink-0">
                        <span>Stan: {{ $activeStall->name ?? 'Terpilih' }}</span>
                        <a href="{{ route('menu.index', request()->except('stall', 'page')) }}" class="hover:text-black">✕</a>
                    </span>
                @endif
                @if($categorySlug && $categorySlug !== 'semua')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-red-50 text-[#ed1e28] border border-red-200 text-[10px] font-bold shrink-0">
                        <span>Kategori: {{ ucfirst($categorySlug) }}</span>
                        <a href="{{ route('menu.index', request()->except('category', 'page')) }}" class="hover:text-black">✕</a>
                    </span>
                @endif
                @if($search)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-red-50 text-[#ed1e28] border border-red-200 text-[10px] font-bold shrink-0">
                        <span>Kata kunci: "{{ $search }}"</span>
                        <a href="{{ route('menu.index', request()->except('q', 'page')) }}" class="hover:text-black">✕</a>
                    </span>
                @endif
                <a href="{{ route('menu.index') }}" class="text-[10px] text-gray-500 hover:text-[#ed1e28] font-bold underline shrink-0">
                    Reset Semua
                </a>
            </div>
        @endif

        <!-- Circular Category Navigation (Matching Screenshot 1) -->
        <div class="flex items-center justify-between gap-1 overflow-x-auto no-scrollbar py-1">
            
            <!-- All / Semua -->
            @php $isAll = empty($categorySlug) || $categorySlug === 'semua'; @endphp
            <a href="{{ route('menu.index', array_merge(request()->except('category', 'page'), ['category' => 'semua'])) }}" class="flex flex-col items-center gap-1.5 shrink-0 group">
                <div class="w-12 h-12 rounded-full flex items-center justify-center transition shadow-xs {{ $isAll ? 'bg-[#ed1e28] text-white' : 'bg-gray-100 text-gray-700 group-hover:bg-gray-200' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M4 4h4v4H4V4zm6 0h4v4h-4V4zm6 0h4v4h-4V4zM4 10h4v4H4v-4zm6 0h4v4h-4v-4zm6 0h4v4h-4v-4zM4 16h4v4H4v-4zm6 0h4v4h-4v-4zm6 0h4v4h-4v-4z"/></svg>
                </div>
                <span class="text-[11px] font-bold {{ $isAll ? 'text-[#ed1e28]' : 'text-gray-600' }}">Semua</span>
            </a>

            <!-- Categories -->
            @foreach($categories as $cat)
                @php $isActive = $categorySlug === $cat->slug; @endphp
                <a href="{{ route('menu.index', array_merge(request()->except('category', 'page'), ['category' => $cat->slug])) }}" class="flex flex-col items-center gap-1.5 shrink-0 group">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg transition shadow-xs {{ $isActive ? 'bg-[#ed1e28] text-white' : 'bg-gray-100 text-gray-700 group-hover:bg-gray-200' }}">
                        <span>{{ $cat->icon ?? '🍽️' }}</span>
                    </div>
                    <span class="text-[11px] font-bold {{ $isActive ? 'text-[#ed1e28]' : 'text-gray-600' }}">{{ $cat->name }}</span>
                </a>
            @endforeach

        </div>

        <!-- Section: Stan Kantin Buka (Slide Warung & Lihat Semua Modal) -->
        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="text-xs sm:text-sm font-black text-[#1a1c1d]">Stan Kantin Buka</h2>
                    <span class="text-[10px] font-bold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200">
                        {{ $stalls->where('is_open', true)->count() }} Aktif
                    </span>
                </div>
                <button type="button" onclick="openAllStallsModal()" class="text-[11px] font-bold text-[#ed1e28] hover:underline flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <span>→</span>
                </button>
            </div>

            <!-- Smooth Horizontal Slider Container for Stalls -->
            <div class="flex items-stretch gap-3 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory pb-2 pt-0.5">
                @foreach($stalls as $stl)
                    <a href="{{ route('menu.index', array_merge(request()->except('stall', 'page'), ['stall' => $stl->id])) }}" 
                       class="snap-start shrink-0 w-52 sm:w-56 bg-white rounded-2xl p-3 border {{ (int)$stallId === $stl->id ? 'border-[#ed1e28] ring-2 ring-[#ed1e28]/20 bg-red-50/10' : 'border-gray-100' }} shadow-2xs hover:shadow-md transition space-y-2 flex flex-col justify-between">
                        
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold {{ $stl->is_open ? 'text-emerald-700 bg-emerald-50' : 'text-gray-500 bg-gray-100' }} px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $stl->is_open ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $stl->is_open ? 'Buka' : 'Tutup' }}
                                </span>
                                <span class="text-[10px] font-bold text-amber-600 flex items-center gap-0.5">
                                    ★ {{ $stl->rating ?? '4.9' }}
                                </span>
                            </div>

                            <h3 class="text-xs font-black text-[#1a1c1d] truncate">{{ $stl->name }}</h3>
                            <p class="text-[10px] text-gray-500 line-clamp-1">{{ $stl->location }} • {{ $stl->category_desc ?? 'Aneka Nasi & Lauk' }}</p>
                        </div>

                        <div class="flex items-center justify-between text-[10px] text-gray-400 pt-1.5 border-t border-gray-50">
                            <span>Estimasi Siap</span>
                            <span class="font-bold text-gray-700">{{ $stl->estimated_time ?? '10-15 mnt' }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Section: Menu Populer Kampus (Matching Screenshot 1) -->
        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xs sm:text-sm font-black text-[#1a1c1d]">Menu Populer Kampus</h2>
                    <p class="text-[10px] text-gray-400">Paling sering dipesan mahasiswa minggu ini</p>
                </div>
                <a href="{{ route('menu.index', array_merge(request()->except('category', 'page'), ['category' => 'semua'])) }}" class="text-[11px] font-bold text-[#ed1e28] hover:underline">
                    Semua
                </a>
            </div>

            <!-- Vertical List of Compact Food Cards -->
            <div class="space-y-2.5" id="menuCardsList">
                @forelse($menus as $menu)
                    <div class="menu-food-card bg-white rounded-2xl p-3 border border-gray-100 shadow-2xs hover:shadow-xs transition flex gap-3 items-center justify-between"
                         data-name="{{ strtolower($menu->name) }}"
                         data-stall="{{ strtolower($menu->stall->name ?? '') }}"
                         data-category="{{ strtolower($menu->category->name ?? '') }}">
                        
                        <!-- Left: Food Photo with prep time overlay badge -->
                        <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden bg-gray-100 shrink-0 cursor-pointer" onclick="openMenuModal({{ $menu->id }})">
                            <img src="{{ asset($menu->photo ?? 'assets/figma/6a55e.png') }}" alt="{{ $menu->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                            <span class="absolute top-1.5 left-1.5 bg-black/60 backdrop-blur-md text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md flex items-center gap-0.5">
                                <span>⏱</span>
                                <span>{{ $menu->prep_time }}</span>
                            </span>
                        </div>

                        <!-- Center: Details -->
                        <div class="flex-1 min-w-0 pr-1">
                            
                            <!-- Stall Name Pill & Rating -->
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span class="inline-block bg-red-50 text-[#ed1e28] text-[9px] font-bold px-2 py-0.5 rounded-full truncate max-w-[120px]">
                                    {{ $menu->stall ? $menu->stall->name : 'Kantin Tel-U' }}
                                </span>
                                <span class="text-[10px] font-bold text-amber-500 shrink-0 flex items-center gap-0.5">
                                    ★ {{ $menu->stall ? $menu->stall->rating : '4.9' }}
                                </span>
                            </div>

                            <h3 class="text-xs font-black text-[#1a1c1d] truncate cursor-pointer hover:text-[#ed1e28] transition" onclick="openMenuModal({{ $menu->id }})">
                                {{ $menu->name }}
                            </h3>

                            <p class="text-[10px] text-gray-500 line-clamp-1 mt-0.5">
                                {{ $menu->description ?? 'Hidangan lezat khas kantin Telkom University.' }}
                            </p>

                            <!-- Bottom Row: Price & + Tambah Button -->
                            <div class="flex items-center justify-between mt-2 pt-1 border-t border-gray-50">
                                <div>
                                    <div class="text-[9px] text-gray-400">Harga</div>
                                    <div class="text-xs font-black text-[#ed1e28]">
                                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                                    </div>
                                </div>

                                <button type="button" onclick="openMenuModal({{ $menu->id }})" class="px-3 py-1.5 bg-[#ed1e28] hover:bg-[#d41720] text-white text-[11px] font-bold rounded-xl shadow-xs active:scale-95 transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>Tambah</span>
                                </button>
                            </div>

                        </div>

                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-8 text-center text-xs text-gray-400 border border-gray-100">
                        Tidak ada menu yang sesuai dengan filter atau kata kunci pencarian.
                    </div>
                @endforelse
            </div>
        </div>

    </main>

    <!-- Modal "Lihat Semua Stan Kantin" -->
    <div id="allStallsModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4">
        <div class="bg-white w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl border border-gray-100 animate-in fade-in zoom-in-95 duration-200 max-h-[90vh] flex flex-col justify-between">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-[#1a1c1d]">Semua Mitra Stan Kantin Tel-U</h3>
                    <p class="text-[10px] text-gray-500">Pilih stan untuk melihat hidangan yang disajikan</p>
                </div>
                <button type="button" onclick="closeAllStallsModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold">✕</button>
            </div>

            <div class="p-4 space-y-2.5 overflow-y-auto flex-1">
                <a href="{{ route('menu.index', request()->except('stall', 'page')) }}" class="block p-3 rounded-2xl border border-gray-200 hover:border-[#ed1e28] transition bg-gray-50 text-center text-xs font-black text-[#1a1c1d]">
                    🍽 Tampilkan Semua Stan & Menu
                </a>

                @foreach($stalls as $stl)
                    <a href="{{ route('menu.index', array_merge(request()->except('stall', 'page'), ['stall' => $stl->id])) }}" 
                       class="p-3 rounded-2xl border {{ (int)$stallId === $stl->id ? 'border-[#ed1e28] bg-red-50/20' : 'border-gray-100 bg-white' }} shadow-2xs hover:shadow-xs flex items-center justify-between gap-3 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-2xl bg-red-50 text-[#ed1e28] flex items-center justify-center font-bold text-lg shrink-0">
                                🏪
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-black text-[#1a1c1d] truncate">{{ $stl->name }}</h4>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded {{ $stl->is_open ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                        ● {{ $stl->is_open ? 'Buka' : 'Tutup' }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-gray-500 truncate">{{ $stl->location }} • {{ $stl->code ?? 'A-01' }}</p>
                                <div class="text-[9px] text-gray-400 mt-0.5">⏱ {{ $stl->opening_hours ?? '07.30 - 17.00 WIB' }}</div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <div class="text-xs font-bold text-amber-500">★ {{ $stl->rating ?? '4.9' }}</div>
                            <span class="inline-block mt-1 px-2.5 py-1 rounded-xl bg-red-50 hover:bg-[#ed1e28] hover:text-white text-[#ed1e28] text-[10px] font-black transition">
                                Pilih Stan →
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal Filter & Pencarian Menu -->
    <div id="filterModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4">
        <div class="bg-white w-full max-w-md rounded-3xl overflow-hidden shadow-2xl border border-gray-100 animate-in fade-in zoom-in-95 duration-200">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-xs sm:text-sm font-black text-[#1a1c1d]">Filter Menu & Stan Kantin</h3>
                <button type="button" onclick="closeFilterModal()" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 flex items-center justify-center text-xs font-bold">✕</button>
            </div>

            <form method="GET" action="{{ route('menu.index') }}" class="p-4 space-y-4">
                @if($search)
                    <input type="hidden" name="q" value="{{ $search }}">
                @endif

                <!-- Filter Kategori -->
                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-2">Kategori Menu</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer border border-gray-200 rounded-xl p-2 text-center text-xs font-bold has-checked:border-[#ed1e28] has-checked:bg-red-50 has-checked:text-[#ed1e28] transition">
                            <input type="radio" name="category" value="semua" {{ empty($categorySlug) || $categorySlug === 'semua' ? 'checked' : '' }} class="sr-only">
                            <span>Semua Kategori</span>
                        </label>
                        @foreach($categories as $cat)
                            <label class="cursor-pointer border border-gray-200 rounded-xl p-2 text-center text-xs font-bold has-checked:border-[#ed1e28] has-checked:bg-red-50 has-checked:text-[#ed1e28] transition">
                                <input type="radio" name="category" value="{{ $cat->slug }}" {{ $categorySlug === $cat->slug ? 'checked' : '' }} class="sr-only">
                                <span>{{ $cat->icon }} {{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Filter Stan -->
                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Stan Kantin</label>
                    <select name="stall" class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                        <option value="">Semua Stan Kantin</option>
                        @foreach($stalls as $stl)
                            <option value="{{ $stl->id }}" {{ (int)$stallId === $stl->id ? 'selected' : '' }}>
                                {{ $stl->name }} ({{ $stl->code ?? 'Kantin' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-2 border-t border-gray-100 flex items-center justify-between gap-3">
                    <a href="{{ route('menu.index') }}" class="px-4 py-2 text-xs font-bold text-gray-500 hover:text-red-600 transition">Reset</a>
                    <button type="submit" class="px-5 py-2.5 bg-[#ed1e28] hover:bg-[#d41720] text-white text-xs font-black rounded-xl shadow-md transition">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Customize Order (Matching Screenshot 3) -->
    <div id="menuModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4">
        <div class="bg-white w-full max-w-md rounded-3xl overflow-hidden shadow-2xl border border-gray-100 animate-in fade-in zoom-in-95 duration-200 max-h-[94vh] flex flex-col justify-between">
            
            <!-- Modal Header with Back Arrow & User Icon -->
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeMenuModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </button>
                    <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Logo Kantin Tel-U" class="w-6 h-6 object-contain drop-shadow-2xs shrink-0" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                    <span class="text-xs font-black text-[#1a1c1d]">Detail Stan & Menu</span>
                </div>
                <button type="button" onclick="closeMenuModal()" class="text-xs font-bold text-gray-400 hover:text-gray-600">✕ Tutup</button>
            </div>

            <!-- Modal Scrollable Body -->
            <div class="p-4 space-y-4 overflow-y-auto flex-1">
                
                <!-- Large Food Photo Card -->
                <div class="relative w-full h-48 rounded-2xl overflow-hidden bg-gray-100 border border-gray-100 shadow-2xs">
                    <img id="modalPhoto" src="{{ asset('assets/figma/6a55e.png') }}" alt="Menu Image" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                    <span id="modalStockBadge" class="absolute bottom-2.5 right-2.5 bg-white/95 backdrop-blur-md px-2.5 py-0.5 rounded-full text-[10px] font-black text-emerald-700 shadow-xs border border-white/60">
                        🟢 Tersedia
                    </span>
                </div>

                <!-- Stall Info Card -->
                <div class="bg-gray-50/80 rounded-2xl p-3 border border-gray-200/70 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-red-100 text-[#ed1e28] flex items-center justify-center text-lg shrink-0">
                            🏪
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <h4 id="modalStallName" class="text-xs font-black text-[#1a1c1d]">Warung Bu Siti</h4>
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded">● Stan Buka</span>
                            </div>
                            <div id="modalStallLocation" class="text-[10px] text-gray-500 mt-0.5">Lantai 1, Blok A Kantin GKU Tel-U</div>
                        </div>
                    </div>
                    <span class="text-[9px] font-bold text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded-md">Verified</span>
                </div>

                <!-- Title & Price (No discount gimmick) -->
                <div>
                    <h3 id="modalName" class="text-sm sm:text-base font-black text-[#1a1c1d] leading-snug">Nama Menu Hidangan</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span id="modalPrice" class="text-base font-black text-[#ed1e28]">Rp 15.000</span>
                    </div>
                </div>

                <!-- 4 Quick Stat Badges (Matching Screenshot 3) -->
                <div class="grid grid-cols-4 gap-1.5 text-center">
                    <div class="bg-gray-50 p-2 rounded-xl border border-gray-200/60">
                        <div class="text-[9px] text-gray-400">⏱ Masak</div>
                        <div id="modalStatPrep" class="text-[11px] font-black text-[#1a1c1d] mt-0.5">8-12 Mnt</div>
                    </div>
                    <div class="bg-gray-50 p-2 rounded-xl border border-gray-200/60">
                        <div class="text-[9px] text-gray-400">🌶 Pedas</div>
                        <div id="modalStatSpice" class="text-[11px] font-black text-[#1a1c1d] mt-0.5">Lvl 1 - 5</div>
                    </div>
                    <div class="bg-gray-50 p-2 rounded-xl border border-gray-200/60">
                        <div class="text-[9px] text-gray-400">📋 Antrean</div>
                        <div class="text-[11px] font-black text-[#1a1c1d] mt-0.5">3 Pesan</div>
                    </div>
                    <div class="bg-gray-50 p-2 rounded-xl border border-gray-200/60">
                        <div class="text-[9px] text-gray-400">★ Rating</div>
                        <div class="text-[11px] font-black text-amber-500 mt-0.5">4.9 (240+)</div>
                    </div>
                </div>

                <!-- Deskripsi Menu -->
                <div>
                    <div class="text-xs font-bold text-[#1a1c1d] mb-1">Deskripsi Menu</div>
                    <p id="modalDescription" class="text-xs text-[#5b5c61] leading-relaxed bg-[#faf9fb] p-2.5 rounded-xl border border-gray-100">
                        Deskripsi menu...
                    </p>
                </div>

                <form id="addToCartForm" method="POST" action="{{ route('cart.add') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="menu_id" id="modalMenuId" value="">

                    <!-- Spice Levels (Khusus Makanan - Matching Screenshot 3) -->
                    <div id="spiceLevelSection">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-[#1a1c1d]">Level Pedas:</label>
                            <span class="text-[9px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Pilih 1</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2" id="spiceOptionsContainer">
                            <label class="cursor-pointer border border-gray-200 rounded-xl p-2.5 text-center has-checked:border-[#ed1e28] has-checked:bg-[#ed1e28] has-checked:text-white transition">
                                <input type="radio" name="spice_level" value="Level 1 Santai" class="sr-only">
                                <div class="text-[11px] font-bold">Level 1</div>
                                <div class="text-[9px] opacity-80">Santai</div>
                            </label>
                            <label class="cursor-pointer border border-gray-200 rounded-xl p-2.5 text-center has-checked:border-[#ed1e28] has-checked:bg-[#ed1e28] has-checked:text-white transition">
                                <input type="radio" name="spice_level" value="Level 3 Sedang" checked class="sr-only">
                                <div class="text-[11px] font-bold">Level 3</div>
                                <div class="text-[9px] opacity-80">Sedang 🔥</div>
                            </label>
                            <label class="cursor-pointer border border-gray-200 rounded-xl p-2.5 text-center has-checked:border-[#ed1e28] has-checked:bg-[#ed1e28] has-checked:text-white transition">
                                <input type="radio" name="spice_level" value="Level 5 Nampol" class="sr-only">
                                <div class="text-[11px] font-bold">Level 5</div>
                                <div class="text-[9px] opacity-80">Nampol 🔥🔥</div>
                            </label>
                        </div>
                    </div>

                    <!-- Portion Options (Matching Screenshot 3) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-[#1a1c1d]">Porsi Nasi / Menu:</label>
                            <span class="text-[9px] text-gray-400">Pilihan Karbo</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer border border-gray-200 rounded-xl p-2.5 has-checked:border-gray-900 has-checked:bg-[#1a1c1d] has-checked:text-white transition flex items-center justify-between">
                                <input type="radio" name="portion" value="Porsi Reguler" checked class="sr-only">
                                <div>
                                    <div class="text-xs font-bold">Nasi Putih</div>
                                    <div class="text-[9px] opacity-70">Porsi Reguler</div>
                                </div>
                                <span class="text-[10px] font-bold opacity-90">Standard</span>
                            </label>
                            <label class="cursor-pointer border border-gray-200 rounded-xl p-2.5 has-checked:border-gray-900 has-checked:bg-[#1a1c1d] has-checked:text-white transition flex items-center justify-between">
                                <input type="radio" name="portion" value="Porsi Jumbo" class="sr-only">
                                <div>
                                    <div class="text-xs font-bold">Nasi Jumbo</div>
                                    <div class="text-[9px] opacity-70">Kenyang Maksimal</div>
                                </div>
                                <span class="text-[10px] font-bold text-[#ed1e28] has-checked:text-red-300">+Rp 3rb</span>
                            </label>
                        </div>
                    </div>

                    <!-- Catatan Khusus Stan (Matching Screenshot 3) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-bold text-[#1a1c1d]">Catatan Khusus Stan:</label>
                            <span class="text-[9px] text-gray-400">Opsional</span>
                        </div>
                        <input type="text" name="notes" placeholder="mis. sambal dipisah, tanpa timun, minyak sedikit" class="w-full bg-[#f4f4f6] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                        <div class="text-[9px] text-gray-400 text-right mt-0.5">Maks. 100 karakter</div>
                    </div>

                    <!-- Sticky Bottom Action Bar -->
                    <div class="pt-3 border-t border-gray-100 flex items-center gap-3">
                        
                        <!-- Counter [-] 1 [+] -->
                        <div class="flex items-center gap-2 bg-gray-100 rounded-2xl p-1 shrink-0">
                            <button type="button" onclick="decrementModalQty()" class="w-8 h-8 rounded-xl bg-white shadow-xs font-bold text-sm flex items-center justify-center text-gray-700 active:scale-90">-</button>
                            <input type="number" id="modalQtyInput" name="quantity" value="1" min="1" max="50" class="w-8 text-center font-black text-xs bg-transparent focus:outline-none">
                            <button type="button" onclick="incrementModalQty()" class="w-8 h-8 rounded-xl bg-white shadow-xs font-bold text-sm flex items-center justify-center text-gray-700 active:scale-90">+</button>
                        </div>

                        <!-- Submit CTA -->
                        <button type="submit" class="flex-1 py-3 bg-[#ed1e28] hover:bg-[#d41720] text-white rounded-2xl text-xs font-bold shadow-lg shadow-red-500/20 active:scale-95 transition flex items-center justify-center gap-2">
                            <span>🛒</span>
                            <span>Tambah</span>
                            <span id="modalCtaPrice">Rp 15.000</span>
                        </button>

                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Mobile Bottom Navigation Bar (md:hidden) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 px-6 py-2 shadow-lg flex items-center justify-between">
        <a href="{{ route('menu.index') }}" class="flex flex-col items-center gap-1 text-[#ed1e28]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] font-black">Menu</span>
        </a>

        <a href="{{ route('cart.index') }}" class="flex flex-col items-center gap-1 text-gray-500 relative">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span class="text-[10px] font-bold">Keranjang</span>
            @if($cartCount > 0)
                <span class="absolute -top-1 -right-2 bg-[#ed1e28] text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center">
                    {{ $cartCount }}
                </span>
            @endif
        </a>

        <a href="{{ route('orders.index') }}" class="flex flex-col items-center gap-1 text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="text-[10px] font-bold">Pesanan</span>
        </a>

        @auth
            <a href="{{ route('profile.index') }}" class="flex flex-col items-center gap-1 text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] font-bold">Biodata</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                <span class="text-[10px] font-bold">Masuk</span>
            </a>
        @endauth
    </nav>

</div>

@push('scripts')
<script>
    const allMenusData = @json($allMenus ?? $menus);
    const displayedMenusData = @json($menus);

    // --- LIVE SEARCH AUTOCOMPLETE & RECOMMENDATIONS ---
    function handleSearchAutocomplete(query) {
        const q = (query || '').trim().toLowerCase();
        const dropdown = document.getElementById('searchSuggestionsDropdown');
        const clearBtn = document.getElementById('clearSearchBtn');

        if (clearBtn) {
            clearBtn.classList.toggle('hidden', q.length === 0);
        }

        if (q.length === 0) {
            dropdown.classList.add('hidden');
            dropdown.innerHTML = '';
            // Reset visible cards
            filterCardsOnPage('');
            return;
        }

        // Live filter cards on page immediately
        filterCardsOnPage(q);

        // Find matches across all menu names, stall names, and categories
        const matches = allMenusData.filter(m => {
            const name = (m.name || '').toLowerCase();
            const stall = m.stall ? (m.stall.name || '').toLowerCase() : '';
            const category = m.category ? (m.category.name || '').toLowerCase() : '';
            return name.includes(q) || stall.includes(q) || category.includes(q);
        }).slice(0, 6);

        if (matches.length === 0) {
            dropdown.innerHTML = `
                <div class="p-4 text-center text-xs text-gray-400">
                    Tidak ditemukan rekomendasi untuk "<strong>${escapeHtml(query)}</strong>"
                </div>
            `;
            dropdown.classList.remove('hidden');
            return;
        }

        let html = `
            <div class="px-3.5 py-2 bg-gray-50/80 text-[10px] font-black tracking-wider text-gray-400 uppercase flex items-center justify-between">
                <span>Rekomendasi Menu (${matches.length})</span>
                <span class="text-[9px] text-[#ed1e28]">Klik untuk pesan</span>
            </div>
        `;

        matches.forEach(item => {
            const photoSrc = item.photo ? (item.photo.startsWith('http') || item.photo.startsWith('/') ? item.photo : '/' + item.photo) : '{{ asset("assets/figma/6a55e.png") }}';
            const priceFormatted = 'Rp ' + Number(item.price).toLocaleString('id-ID');
            const stallName = item.stall ? item.stall.name : 'Kantin Tel-U';
            
            // Highlight query in name
            const highlightedName = highlightMatch(item.name, q);

            html += `
                <div onclick="selectSearchRecommendation(${item.id})" class="p-2.5 hover:bg-red-50/40 cursor-pointer flex items-center justify-between gap-2.5 transition group">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-10 h-10 rounded-xl overflow-hidden bg-gray-100 shrink-0 border border-gray-100">
                            <img src="${photoSrc}" alt="${escapeHtml(item.name)}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-black text-[#1a1c1d] group-hover:text-[#ed1e28] truncate transition">${highlightedName}</h4>
                            <div class="text-[10px] text-gray-400 flex items-center gap-1.5 truncate">
                                <span>🏪 ${escapeHtml(stallName)}</span>
                                <span>•</span>
                                <span>⏱ ${item.prep_time || '10 mnt'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-xs font-black text-[#ed1e28]">${priceFormatted}</div>
                        <span class="inline-block mt-0.5 text-[9px] font-bold text-gray-500 bg-gray-100 px-1.5 py-0.2 rounded">+ Pesan</span>
                    </div>
                </div>
            `;
        });

        dropdown.innerHTML = html;
        dropdown.classList.remove('hidden');
    }

    function highlightMatch(text, query) {
        if (!query) return escapeHtml(text);
        const regex = new RegExp('(' + escapeRegex(query) + ')', 'gi');
        return escapeHtml(text).replace(regex, '<mark class="bg-red-100 text-[#ed1e28] font-black p-0 rounded-xs">$1</mark>');
    }

    function escapeHtml(string) {
        return String(string || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function selectSearchRecommendation(menuId) {
        document.getElementById('searchSuggestionsDropdown').classList.add('hidden');
        openMenuModal(menuId);
    }

    function clearLiveSearch() {
        const input = document.getElementById('liveSearchInput');
        input.value = '';
        handleSearchAutocomplete('');
        input.focus();
    }

    function filterCardsOnPage(query) {
        const cards = document.querySelectorAll('.menu-food-card');
        const q = (query || '').toLowerCase().trim();
        cards.forEach(card => {
            if (!q) {
                card.style.display = 'flex';
                return;
            }
            const name = card.getAttribute('data-name') || '';
            const stall = card.getAttribute('data-stall') || '';
            const category = card.getAttribute('data-category') || '';
            const match = name.includes(q) || stall.includes(q) || category.includes(q);
            card.style.display = match ? 'flex' : 'none';
        });
    }

    // Close suggestions dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const searchWrapper = document.getElementById('searchWrapper');
        if (searchWrapper && !searchWrapper.contains(e.target)) {
            const dropdown = document.getElementById('searchSuggestionsDropdown');
            if (dropdown) dropdown.classList.add('hidden');
        }
    });

    // --- MODAL SEMUA STAN KANTIN ---
    function openAllStallsModal() {
        const modal = document.getElementById('allStallsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAllStallsModal() {
        const modal = document.getElementById('allStallsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // --- MODAL FILTER ---
    function openFilterModal() {
        const modal = document.getElementById('filterModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeFilterModal() {
        const modal = document.getElementById('filterModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // --- MODAL CUSTOMIZE PESANAN ---
    function openMenuModal(menuId) {
        const menu = allMenusData.find(m => m.id === menuId) || displayedMenusData.find(m => m.id === menuId);
        if (!menu) return;

        document.getElementById('modalMenuId').value = menu.id;
        document.getElementById('modalName').textContent = menu.name;
        document.getElementById('modalStallName').textContent = menu.stall ? menu.stall.name : 'Kantin Tel-U';
        document.getElementById('modalStallLocation').textContent = menu.stall ? (menu.stall.location || 'Lantai 1, Blok A Kantin GKU Tel-U') : 'Kantin Tel-U';
        
        const formattedPrice = 'Rp ' + Number(menu.price).toLocaleString('id-ID');
        document.getElementById('modalPrice').textContent = formattedPrice;
        document.getElementById('modalCtaPrice').textContent = formattedPrice;

        document.getElementById('modalStockBadge').textContent = '🟢 Tersedia (Sisa ' + menu.stock + ' porsi)';
        document.getElementById('modalStatPrep').textContent = menu.prep_time || '8-12 Mnt';
        document.getElementById('modalDescription').textContent = menu.description || 'Hidangan lezat khas kantin Telkom University.';
        document.getElementById('modalPhoto').src = '{{ asset("") }}' + (menu.photo || 'assets/figma/6a55e.png');
        document.getElementById('modalQtyInput').value = 1;

        // Hide spice level section for drinks (minuman) or items without spice_levels
        const spiceSection = document.getElementById('spiceLevelSection');
        const statSpice = document.getElementById('modalStatSpice');
        if (spiceSection) {
            const isDrink = (menu.category && (menu.category.slug === 'minuman' || (menu.category.name && menu.category.name.toLowerCase().includes('minum'))))
                || !menu.spice_levels 
                || (Array.isArray(menu.spice_levels) && menu.spice_levels.length === 0);
            
            if (isDrink) {
                spiceSection.style.display = 'none';
                if (statSpice) statSpice.textContent = 'Segar 🥤';
                const spiceRadios = spiceSection.querySelectorAll('input[type="radio"]');
                spiceRadios.forEach(r => r.checked = false);
            } else {
                spiceSection.style.display = 'block';
                if (statSpice) statSpice.textContent = 'Lvl 1 - 5';
                const defaultRadio = spiceSection.querySelector('input[value="Level 3 Sedang"]') || spiceSection.querySelector('input[type="radio"]');
                if (defaultRadio) defaultRadio.checked = true;
            }
        }

        const modal = document.getElementById('menuModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeMenuModal() {
        const modal = document.getElementById('menuModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function incrementModalQty() {
        const input = document.getElementById('modalQtyInput');
        input.value = parseInt(input.value || 1) + 1;
    }

    function decrementModalQty() {
        const input = document.getElementById('modalQtyInput');
        if (parseInt(input.value || 1) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }
</script>
@endpush
@endsection
