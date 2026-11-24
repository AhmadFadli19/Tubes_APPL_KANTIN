@extends('layouts.app', ['title' => 'Checkout & Qris Tel U — Kantin Tel-U'])

@section('content')
<div class="w-full min-h-screen bg-[#faf9fb] flex flex-col justify-between pb-44 md:pb-28">

    <!-- Top Header Bar (Matching Screenshot 2) -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-2xs px-4 py-3">
        <div class="max-w-md mx-auto flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('home') }}" class="shrink-0 hover:opacity-85 transition" title="Kantin Tel-U">
                    <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Logo Kantin Tel-U" class="w-8 h-8 object-contain drop-shadow-2xs shrink-0" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                </a>
                <h1 class="text-xs sm:text-sm font-black text-[#1a1c1d]">Checkout & Qris Tel U</h1>
            </div>

            <!-- User Avatar -->
            <a href="{{ route('profile.index') }}" class="w-8 h-8 rounded-full overflow-hidden bg-gray-200 border-2 border-white shadow-xs flex items-center justify-center">
                @if(auth()->user()->avatar)
                    <img src="{{ asset(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                @else
                    <span class="text-xs font-black text-gray-700">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                @endif
            </a>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-md mx-auto w-full px-4 pt-3 space-y-3.5 flex-1">
        
        @if($cartItems->isEmpty())
            <div class="bg-white rounded-3xl p-10 text-center border border-gray-100 shadow-xs my-8">
                <div class="w-16 h-16 rounded-full bg-red-50 text-3xl flex items-center justify-center mx-auto mb-3">
                    🛒
                </div>
                <h2 class="text-sm font-black text-[#1a1c1d]">Keranjang Anda Kosong</h2>
                <p class="text-xs text-[#5b5c61] mt-1 mb-5 leading-relaxed">
                    Yuk pilih menu hidangan lezat khas kantin Telkom University sekarang.
                </p>
                <a href="{{ route('menu.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ed1e28] hover:bg-[#d41720] text-white text-xs font-bold rounded-2xl shadow-lg shadow-red-500/20 active:scale-95 transition">
                    <span>Lihat Daftar Menu</span>
                    <span>→</span>
                </a>
            </div>
        @else
            <!-- Pickup Location Card (Matching Screenshot 2) -->
            <div class="bg-white rounded-2xl p-3.5 border border-gray-100 shadow-2xs flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 text-[#ed1e28] flex items-center justify-center text-lg shrink-0 border border-rose-100">
                        🏪
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] font-black tracking-wider text-[#ed1e28] uppercase">PICKUP MANDIRI</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        </div>
                        <h2 class="text-xs font-black text-[#1a1c1d]">Kantin Terpadu Tel-U</h2>
                        <p class="text-[10px] text-gray-500">Gedung Selaru Lantai 1</p>
                    </div>
                </div>
                <div class="text-gray-400">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>

            <!-- Cart Section Header (Matching Screenshot 2) -->
            <div class="flex items-center justify-between pt-1">
                <div class="flex items-center gap-2">
                    <h2 class="text-xs sm:text-sm font-black text-[#1a1c1d]">Keranjang Pesanan</h2>
                    <span class="bg-rose-50 text-[#ed1e28] text-[10px] font-bold px-2 py-0.5 rounded-full border border-rose-100">
                        {{ $cartItems->sum('quantity') }} Item
                    </span>
                </div>
                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Kosongkan semua pesanan di keranjang?')" class="text-[11px] font-bold text-gray-500 hover:text-red-600 flex items-center gap-1 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Hapus Semua</span>
                    </button>
                </form>
            </div>

            <!-- Cart Items List (Matching Screenshot 2) -->
            <div class="space-y-2.5">
                @foreach($cartItems as $item)
                    <div class="bg-white rounded-2xl p-3 border border-gray-100 shadow-2xs flex gap-3 items-center justify-between">
                        
                        <!-- Left: Item Photo -->
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden bg-gray-50 shrink-0 border border-gray-100">
                            <img src="{{ asset($item->menu->photo ?? 'assets/figma/6a55e.png') }}" alt="{{ $item->menu->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                        </div>

                        <!-- Center & Right -->
                        <div class="flex-1 min-w-0 space-y-1">
                            <div class="flex items-center justify-between gap-1">
                                <div class="flex items-center gap-1 text-[9px] font-bold text-gray-400 uppercase truncate">
                                    <span>🏪</span>
                                    <span class="truncate">{{ $item->menu->stall->name ?? 'Kantin Tel-U' }}</span>
                                </div>
                                <form method="POST" action="{{ route('cart.remove', $item->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-400 hover:text-red-600 p-0.5" title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>

                            <h3 class="text-xs font-black text-[#1a1c1d] truncate">{{ $item->menu->name }}</h3>

                            <!-- Note / Spice Level / Portion details -->
                            <div class="flex items-center gap-1 flex-wrap text-[10px] text-gray-500">
                                @if($item->portion)
                                    <span class="font-semibold text-gray-600">{{ $item->portion }}</span>
                                @endif
                                @if($item->spice_level)
                                    <span class="text-red-600 font-semibold flex items-center gap-0.5">
                                        <span>🌶</span>
                                        <span>{{ $item->spice_level }}</span>
                                    </span>
                                @endif
                                @if($item->notes)
                                    <span class="text-gray-500 italic truncate max-w-[150px]">"{{ $item->notes }}"</span>
                                @elseif(!$item->portion && !$item->spice_level)
                                    <span class="text-gray-400">Tidak ada catatan tambahan</span>
                                @endif
                            </div>

                            <!-- Bottom Row: Price & Quantity Counter -->
                            <div class="flex items-center justify-between pt-1">
                                <div>
                                    <div class="text-xs font-black text-[#ed1e28]">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </div>
                                    @if($item->quantity > 1)
                                        <div class="text-[9px] text-gray-400">@ Rp {{ number_format($item->subtotal / $item->quantity, 0, ',', '.') }}</div>
                                    @endif
                                </div>

                                <!-- Quantity Selector Pill (Matching Screenshot 2) -->
                                <div class="flex items-center gap-1.5 bg-gray-100 rounded-full px-1.5 py-0.5">
                                    <form method="POST" action="{{ route('cart.update', $item->id) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="decrease">
                                        <button type="submit" class="w-6 h-6 rounded-full bg-white shadow-2xs font-bold text-xs text-gray-700 flex items-center justify-center active:scale-90">-</button>
                                    </form>

                                    <span class="text-xs font-black w-4 text-center text-[#1a1c1d]">{{ $item->quantity }}</span>

                                    <form method="POST" action="{{ route('cart.update', $item->id) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="increase">
                                        <button type="submit" class="w-6 h-6 rounded-full bg-white shadow-2xs font-bold text-xs text-gray-700 flex items-center justify-center active:scale-90">+</button>
                                    </form>
                                </div>
                            </div>

                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Sistem Ambil Mandiri Cepat Announcement Banner (Matching Screenshot 2) -->
            <div class="p-3 bg-rose-50/70 border border-rose-100 rounded-2xl flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-full bg-[#ed1e28] text-white flex items-center justify-center text-xs shrink-0 mt-0.5 shadow-xs">
                    📢
                </div>
                <div>
                    <h4 class="text-xs font-bold text-[#1a1c1d]">Sistem Ambil Mandiri Cepat</h4>
                    <p class="text-[10px] text-gray-600 mt-0.5 leading-relaxed">
                        Pesanan akan disiapkan oleh stan. Kamu akan menerima nomor antrean digital untuk mengambil langsung di stan tanpa antre berdiri.
                    </p>
                </div>
            </div>

            <!-- Metode Pembayaran Section (Matching Screenshot 2) -->
            <div class="space-y-2">
                <h3 class="text-xs sm:text-sm font-black text-[#1a1c1d]">Metode Pembayaran</h3>

                <!-- QRIS Tel-U Pay Card -->
                <div class="bg-white rounded-2xl p-3.5 border border-gray-100 shadow-2xs flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center font-black text-[10px] tracking-widest shrink-0">
                            QRIS
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-black text-[#1a1c1d]">QRIS Tel-U Pay</span>
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded">● Terverifikasi</span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-0.5">BCA, Mandiri, GoPay, OVO, ShopeePay</p>
                        </div>
                    </div>
                    <div class="w-5 h-5 rounded-full bg-[#ed1e28] text-white flex items-center justify-center text-xs font-bold shadow-xs">
                        ✓
                    </div>
                </div>

                <!-- 100% Cashless Notice -->
                <div class="p-3 bg-rose-50/60 border border-rose-100 rounded-2xl space-y-1">
                    <div class="flex items-center gap-1.5 text-[10px] font-black text-[#ed1e28] uppercase tracking-wider">
                        <span>🔒</span>
                        <span>100% CASHLESS KAMPUS • BEBAS UANG TUNAI</span>
                    </div>
                    <p class="text-[10px] text-gray-600 leading-relaxed">
                        Kantin Tel-U hanya menerima pembayaran non-tunai via QRIS (BCA, Mandiri, GoPay, OVO, ShopeePay, TelkomPay dll) untuk mempermudah verifikasi dan membebaskan admin serta penjual dari pengelolaan uang tunai.
                    </p>
                </div>
            </div>

            <!-- Rincian Pembayaran (Matching Screenshot 2) -->
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs space-y-2.5">
                <h3 class="text-xs font-black text-[#1a1c1d] border-b border-gray-100 pb-2">Rincian Pembayaran</h3>

                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center justify-between text-gray-600">
                        <span>Subtotal Menu ({{ $cartItems->sum('quantity') }} porsi)</span>
                        <span class="font-bold text-[#1a1c1d]">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1">
                            <span>Biaya Layanan Kampus</span>
                            <span class="text-[10px] text-gray-400">ⓘ</span>
                        </span>
                        <span class="font-bold text-[#1a1c1d]">Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1">
                            <span>Biaya Ambil di Stan</span>
                            <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1 py-0.2 rounded">HEMAT</span>
                        </span>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] text-gray-400 line-through">Rp 5.000</span>
                            <span class="font-bold text-emerald-600">GRATIS</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                        <div>
                            <div class="text-[10px] text-gray-400 font-bold">Total Pembayaran</div>
                            <div class="text-base font-black text-[#1a1c1d]">Rp {{ number_format($total, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Bottom Checkout Bar (Matching Screenshot 2) -->
            <div class="fixed bottom-[54px] md:bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 px-4 py-3 shadow-lg">
                <div class="max-w-md mx-auto flex items-center justify-between gap-3">
                    <div>
                        <div class="text-[10px] text-gray-400">Total Tagihan</div>
                        <div class="text-base font-black text-[#ed1e28]">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </div>
                    </div>

                    <form method="POST" action="{{ route('checkout.process') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full bg-[#ed1e28] hover:bg-[#d41720] text-white font-black text-xs py-3 rounded-2xl shadow-lg shadow-red-500/20 active:scale-95 transition flex items-center justify-center gap-2">
                            <span>⚡</span>
                            <span>Bayar Instan via QRIS</span>
                        </button>
                    </form>
                </div>
            </div>

        @endif

    </main>

    <!-- Mobile Bottom Navigation Bar (md:hidden) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-gray-200 px-6 py-2 shadow-lg flex items-center justify-between">
        <a href="{{ route('menu.index') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] font-bold">Menu</span>
        </a>

        <a href="{{ route('cart.index') }}" class="flex flex-col items-center gap-1 text-[#ed1e28] relative">
            <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span class="text-[10px] font-black">Keranjang</span>
            @if(isset($cartItems) && $cartItems->isNotEmpty())
                <span class="absolute -top-1 -right-2 bg-[#ed1e28] text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center">
                    {{ $cartItems->sum('quantity') }}
                </span>
            @endif
        </a>

        <a href="{{ route('orders.index') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="text-[10px] font-bold">Pesanan</span>
        </a>

        @auth
            <a href="{{ route('profile.index') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] font-bold">Biodata</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                <span class="text-[10px] font-bold">Masuk</span>
            </a>
        @endauth
    </nav>

</div>
@endsection
