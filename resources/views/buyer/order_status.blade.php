@extends('layouts.app', ['title' => 'Kantin Tel-U — Status Antrean ' . ($order->queue_number ?? $order->order_code)])

@section('content')
<div class="w-full min-h-screen bg-[#faf9fb] flex flex-col justify-between pb-24 md:pb-16">

    <!-- Top Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-2xs px-4 sm:px-8 py-3.5">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <a href="{{ route('menu.index') }}" class="w-10 h-10 rounded-2xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-[#1a1c1d] active:scale-95 transition">
                <img src="{{ asset('assets/figma/76355.svg') }}" alt="Kembali" class="w-4 h-4">
            </a>
            <div class="text-center">
                <div class="text-[10px] font-black text-[#ed1e28] tracking-widest uppercase">⚡ LIVE STATUS ANTREAN TEL-U</div>
                <div class="text-xs sm:text-sm font-black text-[#1a1c1d]">Kode Pesanan: {{ $order->order_code }}</div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('profile.index') }}" title="Lihat Biodata" class="w-8 h-8 rounded-full bg-[#ed1e28] text-white font-black text-xs flex items-center justify-center shadow-xs hover:opacity-85 transition">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </a>
                <a href="{{ route('orders.receipt', $order->id) }}" title="Struk Digital" class="px-3 py-1.5 rounded-xl bg-red-50 text-[#ed1e28] hover:bg-red-100 transition flex items-center gap-1.5 text-xs font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="hidden sm:inline">Struk</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Responsive Grid -->
    <main class="max-w-4xl mx-auto w-full px-4 sm:px-6 py-6 flex-1">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
            
            <!-- Left Column: Queue Number & Live Tracking -->
            <div class="md:col-span-6 space-y-4">
                
                @if(request('paid') || $order->payment_status === 'lunas')
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-3.5 text-xs flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">✓</span>
                            <div>
                                <div class="font-black text-emerald-900">Pembayaran QRIS Berhasil & Lunas!</div>
                                <div class="text-[11px] text-emerald-700">Tidak perlu konfirmasi lagi, koki stan langsung memproses makanan Anda.</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl p-3.5 text-xs flex items-center justify-between shadow-2xs">
                        <div>
                            <div class="font-black text-amber-900">Menunggu Pembayaran QRIS</div>
                            <div class="text-[11px] text-amber-700">Silakan scan kode QRIS untuk menyelesaikan transaksi.</div>
                        </div>
                        <a href="{{ route('orders.qris', $order->id) }}" class="px-3 py-1.5 bg-[#ed1e28] text-white rounded-xl text-xs font-bold shrink-0">
                            Bayar QRIS
                        </a>
                    </div>
                @endif

                <!-- Hero Queue Number Card -->
                <div class="bg-white rounded-3xl p-6 border border-red-100 shadow-md relative overflow-hidden text-center">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-[#ed1e28]/10 rounded-full blur-xl pointer-events-none"></div>

                    <div class="text-[10px] font-black text-[#5b5c61] uppercase tracking-wider mb-1">
                        NOMOR ANTREAN PESANAN ANDA
                    </div>

                    <div class="flex items-center justify-center gap-3 my-3">
                        <span id="live-queue-num" class="text-5xl sm:text-6xl font-black text-[#ed1e28] tracking-tight">
                            {{ $order->queue_number ?? 'MENUNGGU' }}
                        </span>
                    </div>

                    @if($order->queue_number)
                        <div id="live-remaining-badge" class="inline-block bg-red-50 text-[#ed1e28] text-xs font-black px-3.5 py-1 rounded-full border border-red-200 mb-3">
                            Sisa {{ $remainingQueue }} Antrean di Depan Anda
                        </div>
                    @endif

                    <div class="pt-3 border-t border-gray-100 flex flex-col items-center">
                        <div class="flex items-center gap-1.5 font-bold text-xs sm:text-sm text-[#1a1c1d]">
                            <span>🏪 {{ $order->stall->name }}</span>
                            <span class="w-2 h-2 rounded-full bg-[#2e9e5b]"></span>
                        </div>
                        <div class="text-xs text-[#5b5c61] mt-0.5">
                            {{ $order->stall->location }} • {{ $order->stall->opening_hours }}
                        </div>
                    </div>

                    <div class="mt-4 bg-gray-50 rounded-2xl p-3 flex items-center justify-between text-xs text-[#5b5c61]">
                        <div class="flex items-center gap-1.5 text-emerald-600 font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>QRIS Tel-U Pay Lunas</span>
                        </div>
                        <div class="font-black text-[#1a1c1d]">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Ready For Pickup Alert Banner (Figma Req. SMPEK-430) -->
                <div id="pickup-alert-banner" class="{{ $order->status === 'siap_diambil' ? 'block' : 'hidden' }} bg-gradient-to-r from-emerald-600 to-teal-700 text-white rounded-3xl p-5 shadow-lg animate-bounce-short">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-xl shrink-0">
                            🔔
                        </div>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-wider">PESANAN SIAP DIAMBIL!</h3>
                            <p class="text-xs text-white/90 mt-1 leading-relaxed">
                                Silakan menuju meja etalase <strong>{{ $order->stall->name }}</strong> di {{ $order->stall->location }} dan tunjukkan nomor antrean Anda.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 4-Step Visual Progress Tracker (Figma Frame 18:2457) -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-black text-[#1a1c1d] uppercase tracking-wider">STATUS PEMBUATAN MAKANAN</h3>
                        <span id="live-status-label" class="text-xs font-black px-2.5 py-0.5 rounded-full {{ $order->status === 'siap_diambil' ? 'bg-emerald-100 text-emerald-800' : ($order->status === 'diproses' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') }}">
                            {{ $order->status_label }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        <!-- Step 1: Menunggu Konfirmasi -->
                        <div class="flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black shrink-0 {{ in_array($order->status, ['menunggu', 'diproses', 'siap_diambil', 'selesai']) ? 'bg-[#ed1e28] text-white shadow-sm' : 'bg-gray-200 text-gray-500' }}">
                                ✓
                            </div>
                            <div class="flex-1">
                                <div class="text-xs font-bold text-[#1a1c1d]">Pesanan Diterima & Terbayar</div>
                                <div class="text-[11px] text-[#5b5c61]">Penjual menerima pesanan dan memverifikasi nota antrean</div>
                            </div>
                        </div>

                        <!-- Step 2: Diproses / Dimasak -->
                        <div class="flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black shrink-0 {{ in_array($order->status, ['diproses', 'siap_diambil', 'selesai']) ? 'bg-[#ed1e28] text-white shadow-sm' : 'bg-gray-200 text-gray-500' }}">
                                {{ in_array($order->status, ['diproses', 'siap_diambil', 'selesai']) ? '✓' : '2' }}
                            </div>
                            <div class="flex-1">
                                <div class="text-xs font-bold text-[#1a1c1d]">Sedang Dimasak / Disiapkan</div>
                                <div class="text-[11px] text-[#5b5c61]">Koki stan sedang memasak hidangan segar sesuai pesanan</div>
                            </div>
                        </div>

                        <!-- Step 3: Siap Diambil -->
                        <div class="flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black shrink-0 {{ in_array($order->status, ['siap_diambil', 'selesai']) ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-200 text-gray-500' }}">
                                {{ in_array($order->status, ['siap_diambil', 'selesai']) ? '✓' : '3' }}
                            </div>
                            <div class="flex-1">
                                <div class="text-xs font-bold text-[#1a1c1d]">Siap Diambil di Etalase</div>
                                <div class="text-[11px] text-[#5b5c61]">Makanan telah selesai dan siap di etalase stan kantin</div>
                            </div>
                        </div>

                        <!-- Step 4: Selesai -->
                        <div class="flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black shrink-0 {{ $order->status === 'selesai' ? 'bg-[#1a1c1d] text-white' : 'bg-gray-200 text-gray-500' }}">
                                4
                            </div>
                            <div class="flex-1">
                                <div class="text-xs font-bold text-[#1a1c1d]">Pesanan Selesai</div>
                                <div class="text-[11px] text-[#5b5c61]">Makanan telah dinikmati oleh pembeli</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Detail Pesanan & Actions -->
            <div class="md:col-span-6 space-y-4">
                
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <h3 class="text-xs font-black text-[#1a1c1d] uppercase tracking-wider">RINCIAN HIDANGAN</h3>
                        <span class="text-xs text-gray-500">{{ $order->order_date }}</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex items-start justify-between gap-3 text-xs">
                                <div>
                                    <div class="font-black text-[#1a1c1d]">{{ $item->quantity }}x {{ $item->menu_name }}</div>
                                    <div class="flex items-center gap-1.5 text-[10px] text-gray-500 mt-0.5">
                                        @if($item->portion)
                                            <span class="bg-gray-100 px-1.5 py-0.5 rounded">{{ $item->portion }}</span>
                                        @endif
                                        @if($item->spice_level)
                                            <span class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded">{{ $item->spice_level }}</span>
                                        @endif
                                    </div>
                                    @if($item->notes)
                                        <div class="text-[10px] text-gray-400 italic">"{{ $item->notes }}"</div>
                                    @endif
                                </div>
                                <div class="font-bold text-[#1a1c1d] shrink-0">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-gray-100 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-gray-500">
                            <span>Subtotal</span>
                            <span class="font-bold text-gray-700">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-500">
                            <span>Biaya Layanan</span>
                            <span class="font-bold text-gray-700">Rp {{ number_format($order->service_fee, 0, ',', '.') }}</span>
                        </div>
                        @if($order->discount > 0)
                            <div class="flex items-center justify-between text-emerald-600 font-bold">
                                <span>Diskon Tel-U</span>
                                <span>-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-sm font-black text-[#1a1c1d]">
                            <span>Total Pembayaran</span>
                            <span class="text-base text-[#ed1e28]">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation CTAs -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('orders.receipt', $order->id) }}" class="flex-1 py-3.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-800 rounded-2xl text-xs font-bold text-center shadow-xs transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Unduh Struk Transaksi</span>
                    </a>
                    <a href="{{ route('menu.index') }}" class="flex-1 py-3.5 bg-[#ed1e28] hover:bg-[#d41720] text-white rounded-2xl text-xs font-bold text-center shadow-lg shadow-red-500/20 active:scale-95 transition flex items-center justify-center gap-2">
                        <span>Pesan Menu Lain</span>
                        <span>→</span>
                    </a>
                </div>

            </div>

        </div>
    </main>

    <!-- Mobile Bottom Navigation Bar (md:hidden) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-gray-200 px-6 py-2 shadow-lg flex items-center justify-between">
        <a href="{{ route('menu.index') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] font-bold">Menu</span>
        </a>

        <a href="{{ route('cart.index') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span class="text-[10px] font-bold">Keranjang</span>
        </a>

        <a href="{{ route('orders.index') }}" class="flex flex-col items-center gap-1 text-[#ed1e28]">
            <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="text-[10px] font-black">Pesanan</span>
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

@push('scripts')
<script>
    // Live Auto-Polling for Queue Number and Order Status (every 2 seconds)
    const orderId = {{ $order->id }};
    const statusApiUrl = '{{ route("orders.status.api", $order->id) }}';
    let currentStatus = '{{ $order->status }}';

    function playPickupChime() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(523.25, audioCtx.currentTime); // C5
            osc.frequency.setValueAtTime(659.25, audioCtx.currentTime + 0.15); // E5
            osc.frequency.setValueAtTime(783.99, audioCtx.currentTime + 0.3); // G5
            gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.5);
        } catch(e) {}
    }

    setInterval(async () => {
        try {
            const res = await fetch(statusApiUrl);
            if (!res.ok) return;
            const data = await res.json();

            // Update queue number if newly generated
            if (data.queue_number && data.queue_number !== '-') {
                const qNumEl = document.getElementById('live-queue-num');
                if (qNumEl) qNumEl.textContent = data.queue_number;
            }

            // Update remaining queue
            const remBadge = document.getElementById('live-remaining-badge');
            if (remBadge && data.queue_number) {
                remBadge.textContent = 'Sisa ' + data.remaining_queue + ' Antrean di Depan Anda';
            }

            // Update status badge
            const statusBadge = document.getElementById('live-status-label');
            if (statusBadge) {
                statusBadge.textContent = data.status_label;
            }

            // Check if status changed to ready for pickup or selesai
            if (data.status !== currentStatus) {
                if (data.is_ready) {
                    playPickupChime();
                }
                currentStatus = data.status;
                // Reload page to reflect step tracker and actions completely
                setTimeout(() => window.location.reload(), 500);
                return;
            }

            // Pickup banner
            const pickupBanner = document.getElementById('pickup-alert-banner');
            if (pickupBanner && data.is_ready) {
                pickupBanner.classList.remove('hidden');
                pickupBanner.classList.add('block');
            }
        } catch (err) {
            console.error('Polling error:', err);
        }
    }, 2000);
</script>
@endpush
@endsection
