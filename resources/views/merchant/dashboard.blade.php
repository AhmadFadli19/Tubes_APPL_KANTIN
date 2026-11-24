@extends('layouts.merchant')

@section('title', 'Papan Antrean & Pesanan Masuk — Merchant Hub')
@section('merchant_title', 'Papan Pesanan Masuk')

@section('merchant_content')
<div class="px-3 sm:px-6 lg:px-8 py-5 max-w-7xl mx-auto space-y-5">

    <!-- Top Action & Live Status Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white rounded-3xl p-4 sm:p-5 border border-gray-100 shadow-2xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-red-50 text-[#ed1e28] flex items-center justify-center text-lg font-black shrink-0">
                📋
            </div>
            <div>
                <h2 class="text-sm sm:text-base font-black text-[#1a1c1d]">Papan Antrean & Dapur Pesanan</h2>
                <div class="flex items-center gap-2 text-[10px] sm:text-[11px] text-gray-400 mt-0.5">
                    <span class="inline-flex items-center gap-1 font-bold text-emerald-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Auto-Sync Realtime (2 Detik)
                    </span>
                    <span>•</span>
                    <span>{{ $stall->location ?? 'Kantin Gedung TULT' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 self-end sm:self-auto">
            <form method="POST" action="{{ route('merchant.stall.toggle') }}">
                @csrf
                <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold border transition {{ $stall->is_open ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200' }}">
                    {{ $stall->is_open ? '● Stan Buka' : '○ Stan Tutup' }}
                </button>
            </form>
        </div>
    </div>

        <!-- KPI Metrics Overview (Compact on Mobile) -->
        <div id="kpi-metrics-container" class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
            <div class="bg-white rounded-2xl sm:rounded-3xl p-3 sm:p-5 border border-gray-100 shadow-2xs">
                <div class="text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-wider">OMZET HARI INI</div>
                <div class="text-sm sm:text-2xl font-black text-[#ed1e28] mt-0.5 sm:mt-1 truncate">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
                <div class="text-[9px] sm:text-[10px] text-emerald-600 mt-0.5 font-bold truncate">✓ Nontunai QRIS</div>
            </div>

            <div class="bg-white rounded-2xl sm:rounded-3xl p-3 sm:p-5 border border-gray-100 shadow-2xs">
                <div class="text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-wider">TOTAL PESANAN</div>
                <div class="text-sm sm:text-2xl font-black text-[#1a1c1d] mt-0.5 sm:mt-1 truncate">{{ $ordersCount }} Pesanan</div>
                <div class="text-[9px] sm:text-[10px] text-gray-400 mt-0.5 truncate">Hari ini (Semua antrean)</div>
            </div>

            <div class="bg-white rounded-2xl sm:rounded-3xl p-3 sm:p-5 border border-gray-100 shadow-2xs">
                <div class="text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-wider">SEDANG DIMASAK</div>
                <div class="text-sm sm:text-2xl font-black text-amber-600 mt-0.5 sm:mt-1 truncate">{{ $activeQueuesCount }} Antrean</div>
                <div class="text-[9px] sm:text-[10px] text-amber-600 mt-0.5 font-bold truncate">Sedang disiapkan dapur</div>
            </div>

            <div class="bg-white rounded-2xl sm:rounded-3xl p-3 sm:p-5 border border-gray-100 shadow-2xs">
                <div class="text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-wider">MENUNGGU PEMBAYARAN</div>
                <div class="text-sm sm:text-2xl font-black text-rose-600 mt-0.5 sm:mt-1 truncate">{{ $pendingOrders->count() }} Antrean</div>
                <div class="text-[9px] sm:text-[10px] text-rose-600 mt-0.5 font-bold truncate">Belum bayar QRIS / Kasir</div>
            </div>
        </div>

        <!-- Kanban Board for Orders (3 Columns Responsive) -->
        <div id="orders-board-container" class="grid grid-cols-1 lg:grid-cols-3 gap-3.5 sm:gap-6 items-start">
            
            <!-- Column 1: Menunggu Pembayaran / Konfirmasi -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-rose-100 shadow-xs space-y-3 sm:space-y-4">
                <div class="flex items-center justify-between pb-2.5 sm:pb-3 border-b border-rose-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                        <div>
                            <h2 class="text-xs sm:text-sm font-black text-[#1a1c1d]">1. MENUNGGU PEMBAYARAN</h2>
                            <p class="text-[9px] sm:text-[10px] text-gray-400">Belum Bayar • QRIS / Kasir</p>
                        </div>
                    </div>
                    <span id="pending-count-badge" class="bg-rose-100 text-rose-800 text-[11px] sm:text-xs font-black px-2.5 py-0.5 rounded-full">
                        {{ $pendingOrders->count() }}
                    </span>
                </div>

                <!-- Info Banner Auto-Move -->
                <div class="p-2 sm:p-2.5 bg-rose-50/70 border border-rose-100 rounded-xl text-[10px] text-rose-700 leading-relaxed flex items-start gap-1.5">
                    <span class="text-xs shrink-0 mt-0.5">ℹ️</span>
                    <span>Pesanan otomatis pindah ke <strong>Sedang Dimasak</strong> begitu pembeli membayar via QRIS (Live 2s), atau klik konfirmasi di bawah untuk pembayaran tunai di kasir.</span>
                </div>

                @if($pendingOrders->isEmpty())
                    <div class="py-8 text-center text-gray-400 text-xs">
                        Tidak ada pesanan baru yang menunggu pembayaran.
                    </div>
                @else
                    <div class="space-y-2.5 sm:space-y-3">
                        @foreach($pendingOrders as $order)
                            <div class="bg-[#faf9fb] rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-rose-200/80 shadow-2xs space-y-2.5 sm:space-y-3">
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-black text-[#1a1c1d]">{{ $order->order_code }}</span>
                                        <span class="text-[9px] font-bold bg-amber-50 text-amber-700 px-1.5 py-0.2 rounded border border-amber-200">⏳ Belum Bayar</span>
                                    </div>
                                    <span class="text-[10px] text-gray-400">{{ $order->created_at->diffForHumans() }}</span>
                                </div>

                                <div class="text-xs">
                                    <div class="font-bold text-[#1a1c1d]">{{ $order->user->name }}</div>
                                    <div class="text-gray-500 text-[10px] sm:text-[11px]">{{ $order->user->phone ?? 'WA Aktif' }}</div>
                                </div>

                                <!-- Ordered Items List -->
                                <div class="space-y-1 py-1.5 border-y border-gray-200/60 text-xs">
                                    @foreach($order->items as $item)
                                        <div class="flex items-start justify-between gap-1">
                                            <div>
                                                <span class="font-bold text-[#1a1c1d]">{{ $item->quantity }}x {{ $item->menu_name }}</span>
                                                <div class="text-[10px] text-gray-500">
                                                    @if($item->portion) {{ $item->portion }} @endif
                                                    @if($item->spice_level) • {{ $item->spice_level }} @endif
                                                    @if($item->notes) • Note: <span class="text-red-600 font-bold">{{ $item->notes }}</span> @endif
                                                </div>
                                            </div>
                                            <span class="font-bold text-gray-700 shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-gray-500">Total Tagihan:</span>
                                    <span class="font-black text-[#ed1e28]">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                </div>

                                <!-- Konfirmasi Bayar Tunai & Terbitkan Antrean CTA -->
                                <form method="POST" action="{{ route('merchant.orders.confirm', $order->id) }}">
                                    @csrf
                                    <button type="submit" class="w-full bg-[#ed1e28] hover:bg-[#d41720] text-white py-2 rounded-xl text-xs font-black shadow-md shadow-red-500/20 active:scale-95 transition flex items-center justify-center gap-1.5">
                                        <span>Konfirmasi Bayar Kasir (Tunai)</span>
                                        <span>✓</span>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Column 2: Sedang Dimasak / Diproses -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-amber-100 shadow-xs space-y-3 sm:space-y-4">
                <div class="flex items-center justify-between pb-2.5 sm:pb-3 border-b border-amber-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <div>
                            <h2 class="text-xs sm:text-sm font-black text-[#1a1c1d]">2. SEDANG DIMASAK</h2>
                            <p class="text-[9px] sm:text-[10px] text-gray-400">Proses Masak di Dapur</p>
                        </div>
                    </div>
                    <span class="bg-amber-100 text-amber-800 text-[11px] sm:text-xs font-black px-2.5 py-0.5 rounded-full">
                        {{ $inProcessOrders->where('status', 'diproses')->count() }}
                    </span>
                </div>

                @php $cookingOrders = $inProcessOrders->where('status', 'diproses'); @endphp
                @if($cookingOrders->isEmpty())
                    <div class="py-8 text-center text-gray-400 text-xs">
                        Tidak ada antrean yang sedang dimasak saat ini.
                    </div>
                @else
                    <div class="space-y-2.5 sm:space-y-3">
                        @foreach($cookingOrders as $order)
                            <div class="bg-[#faf9fb] rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-amber-200/80 shadow-2xs space-y-2.5 sm:space-y-3">
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-base sm:text-lg font-black text-[#ed1e28] bg-red-50 px-2 sm:px-2.5 py-0.5 rounded-lg border border-red-200">{{ $order->queue_number }}</span>
                                        <span class="text-[10px] text-gray-500">{{ $order->order_code }}</span>
                                    </div>
                                    <span class="text-[10px] text-gray-400">{{ $order->created_at->diffForHumans() }}</span>
                                </div>

                                <div class="text-xs">
                                    <div class="font-bold text-[#1a1c1d]">{{ $order->user->name }}</div>
                                </div>

                                <div class="space-y-1 py-1.5 border-y border-gray-200/60 text-xs">
                                    @foreach($order->items as $item)
                                        <div>
                                            <span class="font-bold text-[#1a1c1d]">{{ $item->quantity }}x {{ $item->menu_name }}</span>
                                            <div class="text-[10px] text-gray-500">
                                                @if($item->portion) {{ $item->portion }} @endif
                                                @if($item->spice_level) • {{ $item->spice_level }} @endif
                                                @if($item->notes) • Note: <span class="text-amber-700 font-bold">{{ $item->notes }}</span> @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Ubah Status ke Siap Diambil -->
                                <form method="POST" action="{{ route('merchant.orders.status', $order->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="siap_diambil">
                                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-xl text-xs font-black shadow-md shadow-emerald-500/20 active:scale-95 transition flex items-center justify-center gap-1.5">
                                        <span>Makanan Siap Diambil 🔔</span>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Column 3: Siap Diambil di Etalase (Dengan Fitur Selesaikan Semua) -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-emerald-100 shadow-xs space-y-3 sm:space-y-4">
                @php $readyOrders = $inProcessOrders->where('status', 'siap_diambil'); @endphp
                <div class="flex items-center justify-between pb-2.5 sm:pb-3 border-b border-emerald-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        <div>
                            <h2 class="text-xs sm:text-sm font-black text-[#1a1c1d]">3. SIAP DI ETALASE</h2>
                            <p class="text-[9px] sm:text-[10px] text-gray-400">Siap Diambil Pembeli</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5">
                        @if($readyOrders->isNotEmpty())
                            <form method="POST" action="{{ route('merchant.orders.complete-all') }}" onsubmit="return confirm('Selesaikan seluruh pesanan siap di etalase sekaligus?')">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[10px] sm:text-[11px] font-black shadow-xs active:scale-95 transition flex items-center gap-1" title="Selesaikan Semua Sekaligus">
                                    <span>✓ Selesaikan Semua</span>
                                </button>
                            </form>
                        @endif
                        <span class="bg-emerald-100 text-emerald-800 text-[11px] sm:text-xs font-black px-2.5 py-0.5 rounded-full">
                            {{ $readyOrders->count() }}
                        </span>
                    </div>
                </div>

                @if($readyOrders->isEmpty())
                    <div class="py-8 text-center text-gray-400 text-xs">
                        Belum ada pesanan yang menunggu diambil di meja etalase.
                    </div>
                @else
                    <div class="space-y-2.5 sm:space-y-3">
                        @foreach($readyOrders as $order)
                            <div class="bg-emerald-50/50 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-emerald-200 shadow-2xs space-y-2.5 sm:space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-base sm:text-lg font-black text-emerald-700 bg-white px-2.5 py-0.5 rounded-xl shadow-xs border border-emerald-200">{{ $order->queue_number }}</span>
                                    <span class="text-[10px] text-emerald-700 font-bold bg-emerald-100/60 px-2 py-0.5 rounded-full">Siap di Etalase</span>
                                </div>

                                <div class="text-xs">
                                    <div class="font-bold text-[#1a1c1d]">{{ $order->user->name }}</div>
                                    <div class="text-[10px] sm:text-[11px] text-gray-500 truncate">{{ $order->items->pluck('menu_name')->implode(', ') }}</div>
                                </div>

                                <!-- Ubah Status ke Selesai -->
                                <form method="POST" action="{{ route('merchant.orders.status', $order->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="selesai">
                                    <button type="submit" class="w-full bg-[#1a1c1d] hover:bg-black text-white py-2 rounded-xl text-xs font-black shadow-md active:scale-95 transition flex items-center justify-center gap-1.5">
                                        <span>Sudah Diambil (Selesai) ✓</span>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

</div>

@push('scripts')
<script>
    let lastPendingCount = {{ $pendingOrders->count() }};
    let isUpdating = false;

    function playOrderChime() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(880, audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1320, audioCtx.currentTime + 0.15);
            gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.35);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.35);
        } catch(e) {}
    }

    // Auto-refresh every 2 seconds (2000 ms)
    setInterval(async () => {
        // Do not interrupt if user is actively filling out any input or form
        if (isUpdating || (document.activeElement && document.activeElement.matches('input, textarea, select'))) {
            return;
        }

        try {
            isUpdating = true;
            const res = await fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const html = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newMetrics = doc.getElementById('kpi-metrics-container');
            const newBoard = doc.getElementById('orders-board-container');

            const curMetrics = document.getElementById('kpi-metrics-container');
            if (newMetrics && curMetrics && curMetrics.innerHTML !== newMetrics.innerHTML) {
                curMetrics.innerHTML = newMetrics.innerHTML;
            }

            const curBoard = document.getElementById('orders-board-container');
            if (newBoard && curBoard && curBoard.innerHTML !== newBoard.innerHTML) {
                curBoard.innerHTML = newBoard.innerHTML;
            }

            const pendingBadge = doc.getElementById('pending-count-badge');
            if (pendingBadge) {
                const count = parseInt(pendingBadge.textContent.trim()) || 0;
                if (count > lastPendingCount) {
                    playOrderChime();
                }
                lastPendingCount = count;
            }
        } catch (err) {
            console.error('Auto refresh error:', err);
        } finally {
            isUpdating = false;
        }
    }, 2000);
</script>
@endpush
@endsection
