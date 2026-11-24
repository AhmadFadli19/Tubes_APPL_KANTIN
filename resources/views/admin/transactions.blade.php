@extends('layouts.app', ['title' => 'Admin — Pantau Transaksi Keseluruhan'])

@section('content')
<div class="w-full min-h-screen bg-[#faf9fb] flex flex-col justify-between pb-24 md:pb-12">

    <!-- Top Navigation Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-gray-200/80 px-4 sm:px-8 py-3.5 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
            <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 sm:gap-3 hover:opacity-85 transition shrink-0" title="Kantin Tel-U">
                <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Tel-U" class="w-8 h-8 sm:w-10 sm:h-10 object-contain shrink-0 drop-shadow-2xs" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <span class="text-xs sm:text-sm font-black text-[#1a1c1d] tracking-tight uppercase hover:text-[#ed1e28] transition truncate">PANTAU TRANSAKSI</span>
                        <span class="bg-emerald-50 text-emerald-700 text-[9px] sm:text-[10px] font-bold px-1.5 sm:px-2 py-0.5 rounded-md shrink-0">LIVE MONITOR 2S</span>
                    </div>
                    <div class="text-[10px] sm:text-[11px] text-[#5b5c61] truncate hidden xs:block">Pengawasan Seluruh Stan & Antrean Aktif Kampus</div>
                </div>
            </a>

            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <nav class="hidden md:flex items-center gap-1.5 lg:gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-[#5b5c61] hover:bg-gray-100 transition">Kelola Akun</a>
                    <a href="{{ route('admin.transactions') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#ed1e28] text-white shadow-2xs">Pantau Transaksi</a>
                    <a href="{{ route('admin.stalls.categories') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-[#5b5c61] hover:bg-gray-100 transition">Stan & Kategori</a>
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
    <main id="admin-transactions-container" class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-6 space-y-6 flex-1">

        <!-- Active Orders Section (Req. SMPEK-630) -->
        <div id="active-orders-section" class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-[#1a1c1d]">Pesanan Aktif di Seluruh Stan</h2>
                    <p class="text-xs text-[#5b5c61]">Real-time sinkronisasi status antrean dan pesanan yang sedang diproses</p>
                </div>
                <span class="bg-red-50 text-[#ed1e28] text-xs font-bold px-3 py-1 rounded-full border border-red-200">
                    {{ $activeOrders->count() }} Antrean Aktif
                </span>
            </div>

            @if($activeOrders->isEmpty())
                <div class="bg-white rounded-2xl p-8 text-center border border-gray-200/80 shadow-2xs">
                    <p class="text-xs text-[#5b5c61]">Belum ada pesanan aktif saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($activeOrders as $order)
                        <div class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-2xs space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-black text-[#ed1e28]">{{ $order->queue_number ?? 'Menunggu' }}</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $order->status === 'siap_diambil' ? 'bg-emerald-100 text-emerald-800 animate-pulse' : 'bg-blue-50 text-blue-700' }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                            <div class="text-xs">
                                <div class="font-bold text-[#1a1c1d]">{{ $order->stall->name }}</div>
                                <div class="text-[#5b5c61]">Pembeli: {{ $order->user->name }} ({{ $order->order_code }})</div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-2 text-[11px] text-[#5b5c61] line-clamp-2">
                                {{ $order->items->pluck('menu_name')->implode(', ') }}
                            </div>
                            <div class="flex items-center justify-between text-xs pt-1 border-t border-gray-100">
                                <span class="text-[#5b5c61]">Total: <strong class="text-[#1a1c1d]">Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></span>
                                <span class="text-[10px] text-gray-400">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- All Transactions History Table -->
        <div class="space-y-3 pt-4">
            <h2 class="text-base font-bold text-[#1a1c1d]">Riwayat Seluruh Transaksi</h2>

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs overflow-hidden">
                <div id="transactions-table-scroller" class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-[#5b5c61] uppercase tracking-wider text-[10px]">
                                <th class="py-2.5 px-3.5 font-bold">Kode / Antrean</th>
                                <th class="py-2.5 px-3.5 font-bold">Stan Kantin</th>
                                <th class="py-2.5 px-3.5 font-bold">Pembeli</th>
                                <th class="py-2.5 px-3.5 font-bold">Daftar Menu</th>
                                <th class="py-2.5 px-3.5 font-bold">Total Pembayaran</th>
                                <th class="py-2.5 px-3.5 font-bold text-center">Status</th>
                                <th class="py-2.5 px-3.5 font-bold text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody id="transactions-table-tbody" class="divide-y divide-gray-100">
                            @forelse($allOrders as $o)
                                <tr class="hover:bg-rose-50/20 transition">
                                    <td class="py-2 px-3.5">
                                        <div class="font-bold text-[#ed1e28]">{{ $o->queue_number ?? '-' }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $o->order_code }}</div>
                                    </td>
                                    <td class="py-2 px-3.5 font-semibold text-[#1a1c1d]">
                                        {{ $o->stall->name }}
                                    </td>
                                    <td class="py-2 px-3.5 text-[#5b5c61]">
                                        {{ $o->user->name }}
                                    </td>
                                    <td class="py-2 px-3.5 text-[#5b5c61] max-w-xs truncate">
                                        {{ $o->items->pluck('menu_name')->implode(', ') }}
                                    </td>
                                    <td class="py-2 px-3.5 font-bold text-[#1a1c1d]">
                                        Rp {{ number_format($o->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2 px-3.5 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                                            {{ $o->status_label }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3.5 text-right text-[#5b5c61]">
                                        {{ $o->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-[#5b5c61]">
                                        Belum ada riwayat transaksi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="transactions-pagination" class="p-4 border-t border-gray-100">
                    {{ $allOrders->links() }}
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

@push('scripts')
<script>
    let isUpdating = false;
    let isUserInteracting = false;
    let interactionTimeout = null;

    // Detect if user is actively touching, dragging, or scrolling the table
    const scroller = document.getElementById('transactions-table-scroller');
    if (scroller) {
        const markInteraction = () => {
            isUserInteracting = true;
            clearTimeout(interactionTimeout);
            interactionTimeout = setTimeout(() => {
                isUserInteracting = false;
            }, 1200); // 1.2s grace period after user stops sliding
        };

        scroller.addEventListener('scroll', markInteraction, { passive: true });
        scroller.addEventListener('touchstart', () => { isUserInteracting = true; }, { passive: true });
        scroller.addEventListener('touchend', markInteraction, { passive: true });
        scroller.addEventListener('mousedown', () => { isUserInteracting = true; }, { passive: true });
        scroller.addEventListener('mouseup', markInteraction, { passive: true });
    }

    // Auto-refresh every 2 seconds without glitching or resetting scroll
    setInterval(async () => {
        if (isUpdating || isUserInteracting || (document.activeElement && document.activeElement.matches('input, textarea, select'))) {
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

            // 1. Update Active Orders only if changed
            const newActiveOrders = doc.getElementById('active-orders-section');
            const currentActiveOrders = document.getElementById('active-orders-section');
            if (newActiveOrders && currentActiveOrders && currentActiveOrders.innerHTML !== newActiveOrders.innerHTML) {
                currentActiveOrders.innerHTML = newActiveOrders.innerHTML;
            }

            // 2. Update Transactions Table Body and preserve scroll position
            const currentScroller = document.getElementById('transactions-table-scroller');
            const savedScrollLeft = currentScroller ? currentScroller.scrollLeft : 0;

            const newTbody = doc.getElementById('transactions-table-tbody');
            const currentTbody = document.getElementById('transactions-table-tbody');
            if (newTbody && currentTbody && currentTbody.innerHTML !== newTbody.innerHTML) {
                currentTbody.innerHTML = newTbody.innerHTML;
                if (currentScroller) {
                    currentScroller.scrollLeft = savedScrollLeft;
                }
            }

            // 3. Update Pagination if changed
            const newPagination = doc.getElementById('transactions-pagination');
            const currentPagination = document.getElementById('transactions-pagination');
            if (newPagination && currentPagination && currentPagination.innerHTML !== newPagination.innerHTML) {
                currentPagination.innerHTML = newPagination.innerHTML;
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
