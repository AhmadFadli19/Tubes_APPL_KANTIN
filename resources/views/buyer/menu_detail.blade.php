@extends('layouts.app', ['title' => $menu->name . ' — Kantin Tel-U'])

@section('content')
<div class="w-full min-h-screen bg-[#faf9fb] flex flex-col justify-between pb-24 sm:pb-12">

    <!-- Top Header Bar (Matching Screenshot 3) -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-2xs px-4 sm:px-8 py-3">
        <div class="max-w-md mx-auto flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('menu.index') }}" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Logo Kantin Tel-U" class="w-7 h-7 object-contain drop-shadow-2xs shrink-0" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                <h1 class="text-xs sm:text-sm font-black text-[#1a1c1d]">Detail Stan & Menu</h1>
            </div>

            <!-- User Avatar -->
            <a href="{{ auth()->check() ? route('profile.index') : route('login') }}" class="w-8 h-8 rounded-full overflow-hidden bg-gray-200 border-2 border-white shadow-xs flex items-center justify-center">
                @if(auth()->check() && auth()->user()->avatar)
                    <img src="{{ asset(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                @elseif(auth()->check())
                    <span class="text-xs font-black text-gray-700">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                @else
                    <span class="text-xs font-bold text-gray-600">👤</span>
                @endif
            </a>
        </div>
    </header>

    @php
        $isDrink = $menu->category && (strtolower($menu->category->slug) === 'minuman' || str_contains(strtolower($menu->category->name), 'minum'));
    @endphp

    <!-- Main Container -->
    <main class="max-w-md mx-auto w-full px-4 pt-3 space-y-4 flex-1">

        <!-- Food Photo Banner with Overlays -->
        <div class="relative w-full h-64 sm:h-72 rounded-3xl overflow-hidden bg-gray-100 border border-gray-100 shadow-md">
            <img src="{{ asset($menu->photo ?? 'assets/figma/6a55e.png') }}" alt="{{ $menu->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
            
            <!-- Back Arrow Overlay -->
            <a href="{{ route('menu.index') }}" class="absolute top-3 left-3 w-9 h-9 rounded-full bg-white/90 backdrop-blur-md text-gray-800 flex items-center justify-center shadow-md hover:bg-white active:scale-95 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>

            <!-- Favorite Heart Overlay -->
            <button type="button" class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/90 backdrop-blur-md text-[#ed1e28] flex items-center justify-center shadow-md hover:bg-white active:scale-95 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>

            <!-- Availability Badge Bottom Right -->
            <span class="absolute bottom-3 right-3 bg-white/95 backdrop-blur-md px-3 py-1 rounded-full text-[10px] font-black text-emerald-700 shadow-sm border border-white/60">
                🟢 Tersedia (Sisa {{ $menu->stock }} porsi)
            </span>
        </div>

        <!-- Stall Info Card (Matching Screenshot 3) -->
        <div class="bg-gray-50/80 rounded-2xl p-3.5 border border-gray-200/80 shadow-2xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-red-100 text-[#ed1e28] flex items-center justify-center text-xl shrink-0">
                    🏪
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xs sm:text-sm font-black text-[#1a1c1d]">{{ $menu->stall->name ?? 'Kantin Tel-U' }}</h2>
                        <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            ● {{ ($menu->stall && $menu->stall->is_open) ? 'Stan Buka' : 'Stan Tutup' }}
                        </span>
                    </div>
                    <div class="text-[10px] text-gray-500 mt-0.5">{{ $menu->stall->location ?? 'Lantai 1, Blok A Kantin GKU Tel-U' }}</div>
                    <div class="text-[10px] text-gray-400 mt-0.5">⏱ {{ $menu->stall->opening_hours ?? '08.00 - 17.00 WIB' }}</div>
                </div>
            </div>
            <span class="text-[9px] font-bold text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded-md shrink-0">Verified Stall</span>
        </div>

        <!-- Food Title & Price -->
        <div>
            <h1 class="text-base sm:text-lg font-black text-[#1a1c1d] leading-snug">{{ $menu->name }}</h1>
            <div class="flex items-center gap-2.5 mt-1">
                <span class="text-xl font-black text-[#ed1e28]">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- 4 Quick Stat Badges (Matching Screenshot 3) -->
        <div class="grid grid-cols-4 gap-2 text-center">
            <div class="bg-gray-50 p-2 rounded-2xl border border-gray-200/60">
                <div class="text-[9px] text-gray-400">⏱ Masak</div>
                <div class="text-xs font-black text-[#1a1c1d] mt-0.5">{{ $menu->prep_time }}</div>
            </div>
            <div class="bg-gray-50 p-2 rounded-2xl border border-gray-200/60">
                <div class="text-[9px] text-gray-400">🌶 Pedas</div>
                <div class="text-xs font-black text-[#1a1c1d] mt-0.5">{{ $isDrink ? 'Segar 🥤' : 'Lvl 1 - 5' }}</div>
            </div>
            <div class="bg-gray-50 p-2 rounded-2xl border border-gray-200/60">
                <div class="text-[9px] text-gray-400">📋 Antrean</div>
                <div class="text-xs font-black text-[#1a1c1d] mt-0.5">3 Pesan</div>
            </div>
            <div class="bg-gray-50 p-2 rounded-2xl border border-gray-200/60">
                <div class="text-[9px] text-gray-400">★ Rating</div>
                <div class="text-xs font-black text-amber-500 mt-0.5">{{ $menu->stall->rating ?? '4.9' }} (240+)</div>
            </div>
        </div>

        <!-- Deskripsi Menu -->
        <div class="space-y-1">
            <h3 class="text-xs font-bold text-[#1a1c1d]">Deskripsi Menu</h3>
            <p class="text-xs text-[#5b5c61] leading-relaxed bg-[#faf9fb] p-3 rounded-2xl border border-gray-100">
                {{ $menu->description ?? 'Hidangan lezat khas kantin Telkom University disajikan hangat untuk Anda.' }}
            </p>
        </div>

        <!-- Order Form -->
        <form method="POST" action="{{ route('cart.add') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="menu_id" value="{{ $menu->id }}">

            @if(! $isDrink && ! empty($menu->spice_levels))
                <!-- Level Pedas Section (Only for Food) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold text-[#1a1c1d]">Level Pedas:</label>
                        <span class="text-[9px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Wajib Pilih 1</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
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
            @endif

            <!-- Portion Options -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-bold text-[#1a1c1d]">Porsi Nasi / Menu:</label>
                    <span class="text-[9px] text-gray-400">Pilihan Karbo</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <label class="cursor-pointer border border-gray-200 rounded-xl p-2.5 has-checked:border-gray-900 has-checked:bg-[#1a1c1d] has-checked:text-white transition flex items-center justify-between">
                        <input type="radio" name="portion" value="Porsi Reguler" checked class="sr-only" onchange="updateDetailPrice()">
                        <div>
                            <div class="text-xs font-bold">Nasi Putih</div>
                            <div class="text-[9px] opacity-70">Porsi Reguler</div>
                        </div>
                        <span class="text-[10px] font-bold opacity-90">Standard</span>
                    </label>
                    <label class="cursor-pointer border border-gray-200 rounded-xl p-2.5 has-checked:border-gray-900 has-checked:bg-[#1a1c1d] has-checked:text-white transition flex items-center justify-between">
                        <input type="radio" name="portion" value="Porsi Jumbo" class="sr-only" onchange="updateDetailPrice()">
                        <div>
                            <div class="text-xs font-bold">Nasi Jumbo</div>
                            <div class="text-[9px] opacity-70">Kenyang Maksimal</div>
                        </div>
                        <span class="text-[10px] font-bold text-[#ed1e28] has-checked:text-red-300">+Rp 3rb</span>
                    </label>
                </div>
            </div>

            <!-- Catatan Khusus Stan -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-bold text-[#1a1c1d]">Catatan Khusus Stan:</label>
                    <span class="text-[9px] text-gray-400">Opsional</span>
                </div>
                <input type="text" name="notes" placeholder="mis. sambal dipisah, tanpa timun, minyak sedikit" class="w-full bg-[#f4f4f6] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                <div class="text-[9px] text-gray-400 text-right mt-0.5">Maks. 100 karakter</div>
            </div>

            <!-- Sticky Bottom Checkout Action -->
            <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 p-3 sm:p-4 shadow-lg">
                <div class="max-w-md mx-auto flex items-center gap-3">
                    
                    <!-- Quantity Counter -->
                    <div class="flex items-center gap-2 bg-gray-100 rounded-2xl p-1 shrink-0">
                        <button type="button" onclick="decrementDetailQty()" class="w-9 h-9 rounded-xl bg-white shadow-xs font-bold text-base flex items-center justify-center text-gray-700 active:scale-90">-</button>
                        <input type="number" id="detailQtyInput" name="quantity" value="1" min="1" max="50" class="w-8 text-center font-black text-xs bg-transparent focus:outline-none" onchange="updateDetailPrice()">
                        <button type="button" onclick="incrementDetailQty()" class="w-9 h-9 rounded-xl bg-white shadow-xs font-bold text-base flex items-center justify-center text-gray-700 active:scale-90">+</button>
                    </div>

                    <!-- Add to Cart CTA -->
                    <button type="submit" class="flex-1 py-3 bg-[#ed1e28] hover:bg-[#d41720] text-white rounded-2xl text-xs font-black shadow-lg shadow-red-500/20 active:scale-95 transition flex items-center justify-center gap-2">
                        <span>🛒</span>
                        <span>Tambah</span>
                        <span id="detailCtaPrice">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                    </button>

                </div>
            </div>

        </form>

    </main>

</div>

@push('scripts')
<script>
    const basePrice = {{ $menu->price }};

    function updateDetailPrice() {
        const qty = parseInt(document.getElementById('detailQtyInput').value) || 1;
        const isJumbo = document.querySelector('input[name="portion"]:checked')?.value === 'Porsi Jumbo';
        const extra = isJumbo ? 3000 : 0;
        const total = (basePrice + extra) * qty;
        document.getElementById('detailCtaPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    function incrementDetailQty() {
        const input = document.getElementById('detailQtyInput');
        input.value = (parseInt(input.value) || 1) + 1;
        updateDetailPrice();
    }

    function decrementDetailQty() {
        const input = document.getElementById('detailQtyInput');
        if ((parseInt(input.value) || 1) > 1) {
            input.value = (parseInt(input.value) || 1) - 1;
            updateDetailPrice();
        }
    }
</script>
@endpush
@endsection
