@extends('layouts.app', ['title' => 'Kantin Tel-U — Riwayat Pesanan'])

@section('content')
<div class="w-full min-h-screen bg-[#faf9fb] flex flex-col justify-between pb-24 md:pb-12">

    <!-- Top Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-2xs px-4 sm:px-8 py-3.5">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 hover:opacity-85 transition" title="Kantin Tel-U">
                    <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Tel-U" class="w-9 h-9 sm:w-10 sm:h-10 object-contain drop-shadow-2xs">
                </a>
                <div>
                    <h1 class="text-sm sm:text-base font-black text-[#1a1c1d]">Riwayat Transaksi & Antrean</h1>
                    <div class="text-[11px] text-[#5b5c61]">Daftar pesanan makanan di seluruh stan kantin Tel-U</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.index') }}" class="flex items-center gap-2 hover:opacity-85 transition" title="Buka Biodata Akun">
                    <div class="w-8 h-8 rounded-full bg-[#ed1e28] text-white font-black text-xs flex items-center justify-center shadow-xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="text-xs font-bold text-[#1a1c1d] hidden sm:inline">{{ explode(' ', auth()->user()->name)[0] }}</span>
                </a>
                <a href="{{ route('menu.index') }}" class="px-3.5 py-2 bg-[#ed1e28] text-white text-xs font-bold rounded-xl shadow-xs hover:bg-[#bb0016] transition">
                    + Pesan Lagi
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main id="order-history-container" class="max-w-5xl mx-auto w-full px-4 sm:px-6 py-6 flex-1 space-y-4">

        @if($orders->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-xs max-w-md mx-auto my-12">
                <div class="w-20 h-20 rounded-full bg-red-50 text-4xl flex items-center justify-center mx-auto mb-4">
                    📋
                </div>
                <h2 class="text-base font-black text-[#1a1c1d]">Belum Ada Riwayat Pesanan</h2>
                <p class="text-xs text-[#5b5c61] mt-1 mb-6 leading-relaxed">
                    Anda belum pernah memesan makanan melalui sistem antrean digital kantin Tel-U.
                </p>
                <a href="{{ route('menu.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#ed1e28] hover:bg-[#d41720] text-white text-xs font-bold rounded-2xl shadow-lg shadow-red-500/20 active:scale-95 transition">
                    <span>Lihat Menu Stan</span>
                    <span>→</span>
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-2xs hover:shadow-md transition flex flex-col sm:flex-row justify-between gap-4">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2.5">
                                <span class="text-xs font-black text-[#ed1e28]">{{ $order->stall->name }}</span>
                                <span class="text-gray-300">•</span>
                                <span class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $order->status === 'siap_diambil' ? 'bg-emerald-100 text-emerald-800' : ($order->status === 'selesai' ? 'bg-gray-100 text-gray-700' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>

                            <div class="text-sm font-black text-[#1a1c1d]">
                                {{ $order->items->pluck('menu_name')->implode(', ') }}
                            </div>

                            <div class="text-xs text-gray-500">
                                Kode: <span class="font-bold text-gray-700">{{ $order->order_code }}</span>
                                @if($order->queue_number)
                                    | No. Antrean: <span class="font-black text-[#ed1e28] text-sm">{{ $order->queue_number }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex sm:flex-col items-center sm:items-end justify-between gap-3 shrink-0 pt-3 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                            <div>
                                <div class="text-[10px] text-gray-400">Total Transaksi</div>
                                <div class="text-sm sm:text-base font-black text-[#ed1e28]">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('orders.receipt', $order->id) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">
                                    Struk
                                </a>
                                <a href="{{ route('orders.show', $order->id) }}" class="px-4 py-1.5 bg-[#ed1e28] hover:bg-[#d41720] text-white rounded-xl text-xs font-bold shadow-sm transition">
                                    Pantau Antrean
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </main>

    <!-- Mobile Bottom Navigation (md:hidden) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 px-6 py-2 shadow-lg flex items-center justify-between">
        <a href="{{ route('menu.index') }}" class="flex flex-col items-center gap-1 text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] font-bold">Menu</span>
        </a>
        <a href="{{ route('cart.index') }}" class="flex flex-col items-center gap-1 text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span class="text-[10px] font-bold">Keranjang</span>
        </a>
        <a href="{{ route('orders.index') }}" class="flex flex-col items-center gap-1 text-[#ed1e28]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="text-[10px] font-black">Pesanan</span>
        </a>
        @auth
            <a href="{{ route('profile.index') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-[#ed1e28] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] font-bold">Biodata</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] font-bold">Masuk</span>
            </a>
        @endauth
    </nav>

</div>

@push('scripts')
<script>
    // If there are active orders, poll every 2 seconds
    const hasActiveOrders = {{ $orders->whereIn('status', ['menunggu', 'diproses', 'siap_diambil'])->isNotEmpty() ? 'true' : 'false' }};
    let isUpdating = false;

    if (hasActiveOrders) {
        setInterval(async () => {
            if (isUpdating) return;
            try {
                isUpdating = true;
                const res = await fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newHistory = doc.getElementById('order-history-container');
                const currentHistory = document.getElementById('order-history-container');
                if (newHistory && currentHistory) {
                    currentHistory.innerHTML = newHistory.innerHTML;
                }
            } catch(e) {
                console.error(e);
            } finally {
                isUpdating = false;
            }
        }, 2000);
    }
</script>
@endpush
@endsection
