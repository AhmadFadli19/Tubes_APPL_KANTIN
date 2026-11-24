@extends('layouts.merchant')

@section('title', 'Kelola Menu & Ketersediaan Stok — Merchant Hub')
@section('merchant_title', 'Kelola Menu')

@section('merchant_content')
<div class="px-4 sm:px-6 lg:px-8 py-5 max-w-7xl mx-auto space-y-6">

    <!-- DESKTOP TITLE & ACTION HEADER (Matching Screenshot 6) -->
    <div class="hidden sm:flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="text-[10px] font-extrabold text-[#ed1e28] uppercase tracking-wider flex items-center gap-1.5">
                <span>📁</span>
                <span>Katalog Stan • {{ strtoupper($stall->code ?? 'Stan A-03') }} ({{ strtoupper($stall->location ?? 'Gd. Cacuk Tel-U') }})</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#1a1c1d] tracking-tight mt-0.5">Kelola Menu & Ketersediaan Stok</h1>
            <p class="text-xs text-gray-500 mt-0.5">Perbarui status ketersediaan, stok harian, dan harga menu stan Anda secara real-time.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <form method="POST" action="{{ route('merchant.menus.clear-all-stock') }}" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan/menonaktifkan semua stok menu hari ini?')">
                @csrf
                <button type="submit" class="px-3.5 py-2.5 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold rounded-2xl shadow-2xs hover:border-red-200 transition flex items-center gap-2">
                    <span class="text-rose-500">🚫</span>
                    <span>Habiskan Semua Stok Hari Ini</span>
                </button>
            </form>

            <button type="button" onclick="openAddMenuModal()" class="px-4 py-2.5 bg-[#ed1e28] hover:bg-[#bb0016] text-white text-xs font-bold rounded-2xl shadow-sm hover:shadow active:scale-95 transition flex items-center gap-2">
                <span class="text-base font-black">+</span>
                <span>Tambah Menu Baru</span>
            </button>
        </div>
    </div>

    <!-- MOBILE TOP BANNER & ACTION ROW (Matching Screenshot 3) -->
    <div class="sm:hidden space-y-3.5">
        <!-- Jam Operasional Banner -->
        <div class="bg-gray-50/90 rounded-2xl px-3.5 py-2 border border-gray-100 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-[11px] font-bold text-gray-700">Jam Operasional Stan</span>
            </div>
            <span class="text-[11px] font-black text-gray-500 bg-white px-2 py-0.5 rounded-lg border border-gray-200">
                {{ $stall->opening_hours ?? '08:00 - 17:00 WIB' }}
            </span>
        </div>

        <!-- Section Title & + Menu Button -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-black text-[#1a1c1d]">Kelola Menu & Stok</h2>
                <p class="text-[11px] text-gray-400">Perbarui ketersediaan menu secara langsung</p>
            </div>
            <button type="button" onclick="openAddMenuModal()" class="px-3.5 py-2 bg-[#ed1e28] text-white text-xs font-bold rounded-xl shadow-xs active:scale-95 transition flex items-center gap-1.5">
                <span class="text-base font-bold">+</span>
                <span>Menu</span>
            </button>
        </div>
    </div>

    <!-- 4 STAT SUMMARY CARDS (Matching Screenshot 6) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- 1. Total Menu Aktif -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-100 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-gray-400 mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500">Total Menu Aktif</span>
                <div class="w-8 h-8 rounded-xl bg-red-50 text-[#ed1e28] flex items-center justify-center text-sm font-bold">
                    🍽️
                </div>
            </div>
            <div>
                <div class="text-lg sm:text-2xl font-black text-[#1a1c1d]">{{ $totalMenus }} <span class="text-xs font-bold text-gray-500">Menu</span></div>
                <div class="text-[10px] sm:text-[11px] font-bold text-emerald-600 mt-1 flex items-center gap-1.5">
                    <span class="px-1.5 py-0.2 bg-emerald-50 rounded">{{ $availableMenus }} Tersedia</span>
                    <span class="text-gray-300">•</span>
                    <span class="text-rose-500">{{ $outOfStockMenus }} Habis</span>
                </div>
            </div>
        </div>

        <!-- 2. Menu Stok Kritis -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-100 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-gray-400 mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500">Menu Stok Kritis</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                    ⚠️
                </div>
            </div>
            <div>
                <div class="text-lg sm:text-2xl font-black text-[#1a1c1d]">{{ $criticalMenus }} <span class="text-xs font-bold text-gray-500">Perlu Restock</span></div>
                <div class="text-[10px] sm:text-[11px] font-bold text-amber-600 mt-1">
                    ↓ Tersisa di bawah 5 porsi
                </div>
            </div>
        </div>

        <!-- 3. Terlaris Hari Ini -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-100 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-gray-400 mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500">Terlaris Hari Ini</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold">
                    🔥
                </div>
            </div>
            <div>
                <div class="text-xs sm:text-sm font-black text-[#1a1c1d] truncate">{{ $bestSellerName }}</div>
                <div class="text-[10px] sm:text-[11px] font-bold text-gray-500 mt-0.5 truncate">Terjual {{ $bestSellerQty }} porsi</div>
            </div>
        </div>

        <!-- 4. Status Stan Otomatis -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-100 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-gray-400 mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500">Status Stan Otomatis</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                    📶
                </div>
            </div>
            <div>
                <div class="text-xs sm:text-sm font-black text-emerald-700 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full {{ ($stall->is_open ?? true) ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    <span>{{ ($stall->is_open ?? true) ? 'Aktif Online' : 'Tutup Sementara' }}</span>
                </div>
                <div class="text-[10px] sm:text-[11px] font-bold text-gray-400 mt-0.5">
                    QRIS Tel-U Pay Siap
                </div>
            </div>
        </div>

    </div>

    <!-- SEARCH & FILTER TOOLBAR (Matching Screenshot 6 & 3) -->
    <div class="bg-white rounded-3xl p-3.5 sm:p-4 border border-gray-100 shadow-2xs space-y-3">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            
            <!-- Search Input -->
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 text-xs">
                    🔍
                </span>
                <input type="text" 
                       id="menuSearchInput" 
                       oninput="filterMenuItems()" 
                       placeholder="Cari nama makanan, minuman, paket..." 
                       class="w-full pl-9 pr-4 py-2 sm:py-2.5 bg-gray-50/80 border border-gray-200 rounded-2xl text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
            </div>

            <!-- Status Dropdown Filter -->
            <div class="hidden sm:flex items-center gap-2">
                <select id="statusFilterSelect" onchange="filterMenuItems()" class="px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold text-gray-700 focus:outline-none focus:border-[#ed1e28] transition">
                    <option value="all">Status: Semua Status ({{ $totalMenus }})</option>
                    <option value="available">Hanya Tersedia ({{ $availableMenus }})</option>
                    <option value="critical">Stok Kritis ({{ $criticalMenus }})</option>
                    <option value="out">Habis ({{ $outOfStockMenus }})</option>
                </select>
            </div>
        </div>

        <!-- Category Horizontal Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pt-1 pb-0.5">
            <button type="button" 
                    onclick="filterCategory('all', this)" 
                    class="category-filter-pill px-4 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap bg-[#ed1e28] text-white shadow-2xs">
                Semua ({{ $totalMenus }})
            </button>
            @foreach($categories as $category)
                @php
                    $countInCat = $menus->where('category_id', $category->id)->count();
                @endphp
                <button type="button" 
                        onclick="filterCategory('{{ $category->id }}', this)" 
                        class="category-filter-pill px-4 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap bg-gray-100 text-gray-600 hover:bg-gray-200">
                    {{ $category->name }} ({{ $countInCat }})
                </button>
            @endforeach
        </div>
    </div>

    <!-- DESKTOP TABLE VIEW (Matching Screenshot 6) -->
    <div class="hidden lg:block bg-white rounded-3xl border border-gray-100 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-[10px] font-black uppercase tracking-wider text-gray-400">
                        <th class="py-3.5 px-6">Detail Menu Makanan & Minuman</th>
                        <th class="py-3.5 px-6">Harga Jual</th>
                        <th class="py-3.5 px-6">Sisa Stok Real-Time</th>
                        <th class="py-3.5 px-6">Status Jual</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="desktopMenuTableBody">
                    @forelse($menus as $menu)
                        @php
                            $isOutOfStock = $menu->stock <= 0 || ! $menu->is_available;
                            $isCritical = $menu->stock > 0 && $menu->stock <= 5;
                            $isDrink = $menu->category && (strtolower($menu->category->slug) === 'minuman' || str_contains(strtolower($menu->category->name), 'minum'));
                        @endphp
                        <tr class="menu-item-row hover:bg-gray-50/60 transition"
                            data-id="{{ $menu->id }}"
                            data-name="{{ strtolower($menu->name) }}"
                            data-category-id="{{ $menu->category_id }}"
                            data-status="{{ $isOutOfStock ? 'out' : ($isCritical ? 'critical' : 'available') }}">
                            
                            <!-- Detail Menu -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3.5">
                                    <div class="relative w-14 h-14 rounded-2xl overflow-hidden bg-gray-100 shrink-0 border border-gray-100">
                                        <img src="{{ asset($menu->photo ?? 'assets/figma/6a55e.png') }}" 
                                             alt="{{ $menu->name }}" 
                                             class="w-full h-full object-cover {{ $isOutOfStock ? 'grayscale opacity-70' : '' }}" 
                                             onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                                        @if($isOutOfStock)
                                            <span class="absolute inset-0 bg-black/50 text-white text-[8px] font-black flex items-center justify-center">HABIS</span>
                                        @elseif($menu->prep_time)
                                            <span class="absolute bottom-1 right-1 bg-black/60 text-white text-[8px] font-bold px-1 rounded">⏱{{ $menu->prep_time }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-xs font-black text-[#1a1c1d] truncate">{{ $menu->name }}</span>
                                            @if($isCritical)
                                                <span class="px-1.5 py-0.2 bg-amber-50 text-amber-700 text-[9px] font-bold rounded">Sisa {{ $menu->stock }}</span>
                                            @elseif(! $isDrink && ! empty($menu->spice_levels))
                                                <span class="px-1.5 py-0.2 bg-red-50 text-[#ed1e28] text-[9px] font-bold rounded">🌶️ Level</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-gray-400 truncate mt-0.5 max-w-sm">
                                            {{ $menu->category->name ?? 'Menu' }} • {{ $menu->description ?? 'Pilihan hidangan favorit khas kantin kampus.' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Harga Jual -->
                            <td class="py-4 px-6 font-black text-sm text-[#1a1c1d] whitespace-nowrap">
                                Rp {{ number_format($menu->price, 0, ',', '.') }}
                            </td>

                            <!-- Sisa Stok Real-Time -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="inline-flex items-center gap-1 bg-gray-50 border border-gray-200 rounded-xl p-1">
                                    <button type="button" 
                                            onclick="adjustStock({{ $menu->id }}, -1)" 
                                            class="w-7 h-7 rounded-lg bg-white border border-gray-200 text-gray-700 font-black text-xs hover:bg-gray-100 active:scale-95 transition flex items-center justify-center">
                                        -
                                    </button>
                                    <span id="stock-val-{{ $menu->id }}" class="w-10 text-center font-black text-xs text-[#1a1c1d]">
                                        {{ $menu->stock }}
                                    </span>
                                    <button type="button" 
                                            onclick="adjustStock({{ $menu->id }}, 1)" 
                                            class="w-7 h-7 rounded-lg bg-white border border-gray-200 text-gray-700 font-black text-xs hover:bg-gray-100 active:scale-95 transition flex items-center justify-center">
                                        +
                                    </button>
                                </div>
                                <div id="stock-label-{{ $menu->id }}" class="text-[10px] font-bold mt-1 {{ $isOutOfStock ? 'text-rose-500' : ($isCritical ? 'text-amber-600' : 'text-emerald-600') }}">
                                    {{ $isOutOfStock ? 'Habis Total' : ($isCritical ? 'Stok Kritis!' : 'Aman (>5 porsi)') }}
                                </div>
                            </td>

                            <!-- Status Jual Toggle -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" 
                                           class="sr-only peer" 
                                           {{ $menu->is_available ? 'checked' : '' }} 
                                           onchange="toggleMenuAvailability({{ $menu->id }}, this)">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 relative"></div>
                                    <span id="avail-label-{{ $menu->id }}" class="text-xs font-bold {{ $menu->is_available ? 'text-emerald-700' : 'text-gray-400' }}">
                                        {{ $menu->is_available ? 'Tersedia' : 'Tutup / Habis' }}
                                    </span>
                                </label>
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" 
                                            onclick="openEditMenuModal({{ json_encode($menu) }})" 
                                            class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center text-xs transition" 
                                            title="Ubah Menu">
                                        ✏️
                                    </button>
                                    <form method="POST" action="{{ route('merchant.menus.delete', $menu->id) }}" class="inline" onsubmit="return confirm('Hapus menu hidangan {{ $menu->name }}?')">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-red-50 text-gray-400 hover:text-red-600 flex items-center justify-center text-xs transition" title="Hapus">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400 text-xs">
                                Belum ada menu hidangan yang ditambahkan. Silakan klik "+ Tambah Menu Baru".
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
            <div>Menampilkan <span id="displayedMenuCount">{{ $totalMenus }}</span> dari {{ $totalMenus }} total menu hidangan.</div>
            <div class="flex items-center gap-1.5 text-emerald-600 font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Sinkronisasi Cloud Aktif</span>
            </div>
        </div>
    </div>

    <!-- MOBILE CARD LIST VIEW (Matching Screenshot 3) -->
    <div class="lg:hidden space-y-3" id="mobileMenuList">
        @forelse($menus as $menu)
            @php
                $isOutOfStock = $menu->stock <= 0 || ! $menu->is_available;
                $isCritical = $menu->stock > 0 && $menu->stock <= 5;
                $isDrink = $menu->category && (strtolower($menu->category->slug) === 'minuman' || str_contains(strtolower($menu->category->name), 'minum'));
            @endphp
            <div class="menu-item-row bg-white rounded-3xl p-3.5 border border-gray-100 shadow-2xs space-y-3"
                 data-id="{{ $menu->id }}"
                 data-name="{{ strtolower($menu->name) }}"
                 data-category-id="{{ $menu->category_id }}"
                 data-status="{{ $isOutOfStock ? 'out' : ($isCritical ? 'critical' : 'available') }}">
                
                <!-- Top Row: Thumbnail + Details + Toggle -->
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="relative w-16 h-16 rounded-2xl overflow-hidden bg-gray-100 shrink-0 border border-gray-100">
                            <img src="{{ asset($menu->photo ?? 'assets/figma/6a55e.png') }}" 
                                 alt="{{ $menu->name }}" 
                                 class="w-full h-full object-cover {{ $isOutOfStock ? 'grayscale opacity-70' : '' }}"
                                 onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                            @if($isOutOfStock)
                                <span class="absolute inset-0 bg-black/60 text-white text-[9px] font-black flex items-center justify-center">HABIS</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-black text-[#1a1c1d] truncate">{{ $menu->name }}</h3>
                            <div class="text-xs font-black text-[#ed1e28] mt-0.5">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="text-[9px] font-black uppercase {{ $isOutOfStock ? 'text-rose-500' : ($isCritical ? 'text-amber-600' : 'text-emerald-600') }}">
                                    {{ $isOutOfStock ? '🔴 HABIS HARI INI' : ($isCritical ? '⚠️ TERSISA SEDIKIT' : '🟢 TERSEDIA') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Toggle Switch & Actions -->
                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" 
                                   class="sr-only peer" 
                                   {{ $menu->is_available ? 'checked' : '' }} 
                                   onchange="toggleMenuAvailability({{ $menu->id }}, this)">
                            <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500 relative"></div>
                        </label>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="openEditMenuModal({{ json_encode($menu) }})" class="p-1 text-gray-400 hover:text-gray-700 text-xs">✏️</button>
                            <form method="POST" action="{{ route('merchant.menus.delete', $menu->id) }}" class="inline" onsubmit="return confirm('Hapus menu ini?')">
                                @csrf
                                <button type="submit" class="p-1 text-gray-300 hover:text-rose-600 text-xs">🗑️</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Bottom Stock Bar (Matching Screenshot 3) -->
                @if($isOutOfStock)
                    <div class="bg-gray-50 rounded-2xl p-2.5 flex items-center justify-between text-xs border border-gray-100">
                        <span class="text-[10px] text-gray-400">Otomatis tidak dapat dipesan</span>
                        <button type="button" onclick="adjustStock({{ $menu->id }}, 10)" class="text-[10px] font-bold text-[#ed1e28] hover:underline">
                            + Setel Ada Lagi
                        </button>
                    </div>
                @else
                    <div class="bg-gray-50/80 rounded-2xl p-2 flex items-center justify-between text-xs border border-gray-100">
                        <div class="text-[11px] font-bold text-gray-600 flex items-center gap-1.5">
                            <span>📦</span>
                            <span>Sisa Stok:</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="inline-flex items-center gap-1 bg-white border border-gray-200 rounded-xl p-0.5 shadow-2xs">
                                <button type="button" onclick="adjustStock({{ $menu->id }}, -1)" class="w-6 h-6 rounded-lg bg-gray-100 text-gray-700 font-black text-xs active:scale-90 flex items-center justify-center">-</button>
                                <span id="mobile-stock-val-{{ $menu->id }}" class="w-8 text-center font-black text-xs text-[#1a1c1d]">{{ $menu->stock }}</span>
                                <button type="button" onclick="adjustStock({{ $menu->id }}, 1)" class="w-6 h-6 rounded-lg bg-gray-100 text-gray-700 font-black text-xs active:scale-90 flex items-center justify-center">+</button>
                            </div>
                            <span class="text-[10px] text-gray-400 font-bold">porsi</span>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 text-center text-gray-400 text-xs">
                Belum ada menu. Tekan tombol "+ Menu" di atas untuk menambahkan.
            </div>
        @endforelse
    </div>

</div>

<!-- MODAL TAMBAH MENU HIDANGAN BARU -->
<div id="addMenuModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-5 sm:p-6 shadow-2xl space-y-4">
        
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-sm sm:text-base font-black text-[#1a1c1d]">Tambah Menu Hidangan Baru</h3>
            <button type="button" onclick="closeAddMenuModal()" class="w-7 h-7 rounded-full bg-gray-100 text-gray-400 hover:text-gray-600 flex items-center justify-center font-bold">✕</button>
        </div>

        <form method="POST" action="{{ route('merchant.menus.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Nama Menu -->
            <div>
                <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Nama Menu Hidangan <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Nasi Ayam Sambal Matah" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
            </div>

            <!-- Kategori & Harga -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" id="addCategorySelect" required onchange="handleCategoryChange(this, 'add')" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" data-slug="{{ strtolower($cat->slug ?? $cat->name) }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Harga (IDR) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" required min="0" step="500" placeholder="15000" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                </div>
            </div>

            <!-- Stok & Estimasi Siap -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Stok Awal (Porsi) <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" required min="0" value="20" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Estimasi Siap (Menit)</label>
                    <input type="text" name="prep_time" value="10 mnt" placeholder="10 mnt" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                </div>
            </div>

            <!-- Deskripsi Menu -->
            <div>
                <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Deskripsi Menu</label>
                <textarea name="description" rows="2" placeholder="Bahan-bahan, cita rasa, dan pelengkap..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"></textarea>
            </div>

            <!-- Foto Menu Upload Preview -->
            <div>
                <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Foto / Poster Menu</label>
                <div class="flex items-center gap-3">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center">
                        <img id="addPhotoPreview" src="{{ asset('assets/figma/6a55e.png') }}" alt="Preview" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                    </div>
                    <div class="flex-1">
                        <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-xs font-bold text-gray-700 transition">
                            <span>📁 Pilih File Gambar</span>
                            <input type="file" name="photo" accept="image/*" class="hidden" onchange="previewMenuPhoto(this, 'addPhotoPreview')">
                        </label>
                        <div class="text-[10px] text-gray-400 mt-1">Maks. 5MB (JPG, PNG, WebP)</div>
                    </div>
                </div>
            </div>

            <!-- Level Pedas & Porsi Jumbo (Khusus Makanan) -->
            <div id="addFoodOptionsBox" class="p-3 bg-red-50/50 rounded-2xl border border-red-100 space-y-2">
                <div class="text-[11px] font-black text-[#ed1e28]">Opsi Khusus Makanan</div>
                <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer">
                    <input type="checkbox" name="has_spice_levels" value="1" checked class="w-4 h-4 text-[#ed1e28] rounded border-gray-300">
                    <span>Aktifkan Pilihan Level Pedas (Level 1, Level 3, Level 5)</span>
                </label>
                <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer">
                    <input type="checkbox" name="has_jumbo_portion" value="1" checked class="w-4 h-4 text-[#ed1e28] rounded border-gray-300">
                    <span>Sediakan Opsi Porsi Jumbo (+ Rp 3.000)</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeAddMenuModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-[#ed1e28] hover:bg-[#bb0016] text-white text-xs font-bold rounded-xl shadow-xs transition">Simpan Menu</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT MENU HIDANGAN -->
<div id="editMenuModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-5 sm:p-6 shadow-2xl space-y-4">
        
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-sm sm:text-base font-black text-[#1a1c1d]">Ubah Menu Hidangan</h3>
            <button type="button" onclick="closeEditMenuModal()" class="w-7 h-7 rounded-full bg-gray-100 text-gray-400 hover:text-gray-600 flex items-center justify-center font-bold">✕</button>
        </div>

        <form id="editMenuForm" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Nama Menu Hidangan <span class="text-red-500">*</span></label>
                <input type="text" id="editMenuName" name="name" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" id="editCategorySelect" required onchange="handleCategoryChange(this, 'edit')" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" data-slug="{{ strtolower($cat->slug ?? $cat->name) }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Harga (IDR) <span class="text-red-500">*</span></label>
                    <input type="number" id="editMenuPrice" name="price" required min="0" step="500" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Sisa Stok (Porsi) <span class="text-red-500">*</span></label>
                    <input type="number" id="editMenuStock" name="stock" required min="0" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Estimasi Siap (Menit)</label>
                    <input type="text" id="editMenuPrepTime" name="prep_time" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Deskripsi Menu</label>
                <textarea id="editMenuDescription" name="description" rows="2" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Foto / Poster Menu</label>
                <div class="flex items-center gap-3">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center">
                        <img id="editPhotoPreview" src="{{ asset('assets/figma/6a55e.png') }}" alt="Preview" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                    </div>
                    <div class="flex-1">
                        <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-xs font-bold text-gray-700 transition">
                            <span>Ganti File Foto</span>
                            <input type="file" name="photo" accept="image/*" class="hidden" onchange="previewMenuPhoto(this, 'editPhotoPreview')">
                        </label>
                        <div class="text-[10px] text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah foto.</div>
                    </div>
                </div>
            </div>

            <div id="editFoodOptionsBox" class="p-3 bg-red-50/50 rounded-2xl border border-red-100 space-y-2">
                <div class="text-[11px] font-black text-[#ed1e28]">Opsi Khusus Makanan</div>
                <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer">
                    <input type="checkbox" id="editHasSpiceCheckbox" name="has_spice_levels" value="1" class="w-4 h-4 text-[#ed1e28] rounded border-gray-300">
                    <span>Aktifkan Pilihan Level Pedas</span>
                </label>
                <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer">
                    <input type="checkbox" id="editHasJumboCheckbox" name="has_jumbo_portion" value="1" class="w-4 h-4 text-[#ed1e28] rounded border-gray-300">
                    <span>Sediakan Opsi Porsi Jumbo (+ Rp 3.000)</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeEditMenuModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-[#ed1e28] hover:bg-[#bb0016] text-white text-xs font-bold rounded-xl shadow-xs transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Preview Image
    function previewMenuPhoto(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Modal Add
    function openAddMenuModal() {
        document.getElementById('addMenuModal').classList.remove('hidden');
        document.getElementById('addMenuModal').classList.add('flex');
    }
    function closeAddMenuModal() {
        document.getElementById('addMenuModal').classList.add('hidden');
        document.getElementById('addMenuModal').classList.remove('flex');
    }

    // Modal Edit
    function openEditMenuModal(menu) {
        document.getElementById('editMenuForm').action = '/merchant/menus/' + menu.id;
        document.getElementById('editMenuName').value = menu.name || '';
        document.getElementById('editMenuPrice').value = menu.price || 0;
        document.getElementById('editMenuStock').value = menu.stock || 0;
        document.getElementById('editMenuPrepTime').value = menu.prep_time || '10 mnt';
        document.getElementById('editMenuDescription').value = menu.description || '';
        
        const photoUrl = menu.photo ? (menu.photo.startsWith('http') || menu.photo.startsWith('/') ? menu.photo : '/' + menu.photo) : '{{ asset("assets/figma/6a55e.png") }}';
        document.getElementById('editPhotoPreview').src = photoUrl;

        const catSelect = document.getElementById('editCategorySelect');
        if (catSelect) {
            catSelect.value = menu.category_id;
            handleCategoryChange(catSelect, 'edit');
        }

        const hasSpice = Array.isArray(menu.spice_levels) ? menu.spice_levels.length > 0 : Boolean(menu.spice_levels);
        document.getElementById('editHasSpiceCheckbox').checked = hasSpice;

        const hasJumbo = Array.isArray(menu.portion_options) ? menu.portion_options.some(p => p.name && p.name.includes('Jumbo')) : false;
        document.getElementById('editHasJumboCheckbox').checked = hasJumbo;

        document.getElementById('editMenuModal').classList.remove('hidden');
        document.getElementById('editMenuModal').classList.add('flex');
    }
    function closeEditMenuModal() {
        document.getElementById('editMenuModal').classList.add('hidden');
        document.getElementById('editMenuModal').classList.remove('flex');
    }

    // Handle Drink vs Food options
    function handleCategoryChange(selectElem, prefix) {
        const selectedOpt = selectElem.options[selectElem.selectedIndex];
        const slug = selectedOpt ? (selectedOpt.getAttribute('data-slug') || '') : '';
        const isDrink = slug.includes('minum');
        const box = document.getElementById(prefix + 'FoodOptionsBox');
        if (box) {
            box.style.display = isDrink ? 'none' : 'block';
        }
    }

    // Adjust Stock AJAX
    let stockTimers = {};
    function adjustStock(menuId, delta) {
        const desktopVal = document.getElementById('stock-val-' + menuId);
        const mobileVal = document.getElementById('mobile-stock-val-' + menuId);
        let currentStock = parseInt(desktopVal ? desktopVal.innerText : (mobileVal ? mobileVal.innerText : 0));
        let newStock = Math.max(0, currentStock + delta);
        
        if (desktopVal) desktopVal.innerText = newStock;
        if (mobileVal) mobileVal.innerText = newStock;

        // Debounce update to server
        clearTimeout(stockTimers[menuId]);
        stockTimers[menuId] = setTimeout(() => {
            fetch('/merchant/menus/' + menuId + '/stock', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ stock: newStock })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    const label = document.getElementById('stock-label-' + menuId);
                    if (label) {
                        label.innerText = newStock <= 0 ? 'Habis Total' : (newStock <= 5 ? 'Stok Kritis!' : 'Aman (>5 porsi)');
                        label.className = 'text-[10px] font-bold mt-1 ' + (newStock <= 0 ? 'text-rose-500' : (newStock <= 5 ? 'text-amber-600' : 'text-emerald-600'));
                    }
                }
            });
        }, 350);
    }

    // Toggle Availability AJAX
    function toggleMenuAvailability(menuId, checkbox) {
        fetch('/merchant/menus/' + menuId + '/stock', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ toggle_available: true })
        }).then(r => r.json()).then(data => {
            const label = document.getElementById('avail-label-' + menuId);
            if (label) {
                label.innerText = data.is_available ? 'Tersedia' : 'Tutup / Habis';
                label.className = 'text-xs font-bold ' + (data.is_available ? 'text-emerald-700' : 'text-gray-400');
            }
        });
    }

    // Filter Items by Category & Search
    let selectedCategoryId = 'all';
    function filterCategory(catId, btn) {
        selectedCategoryId = catId;
        document.querySelectorAll('.category-filter-pill').forEach(b => {
            b.className = 'category-filter-pill px-4 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap bg-gray-100 text-gray-600 hover:bg-gray-200';
        });
        btn.className = 'category-filter-pill px-4 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap bg-[#ed1e28] text-white shadow-2xs';
        filterMenuItems();
    }

    function filterMenuItems() {
        const query = (document.getElementById('menuSearchInput').value || '').toLowerCase().trim();
        const status = document.getElementById('statusFilterSelect') ? document.getElementById('statusFilterSelect').value : 'all';
        
        let visibleCount = 0;
        document.querySelectorAll('.menu-item-row').forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const rowCat = row.getAttribute('data-category-id') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchQuery = !query || name.includes(query);
            const matchCat = selectedCategoryId === 'all' || rowCat === selectedCategoryId;
            const matchStatus = status === 'all' || rowStatus === status;

            if (matchQuery && matchCat && matchStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const countSpan = document.getElementById('displayedMenuCount');
        if (countSpan) countSpan.innerText = visibleCount;
    }
</script>
@endsection
