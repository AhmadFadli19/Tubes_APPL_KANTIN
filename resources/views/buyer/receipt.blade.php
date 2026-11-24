@extends('layouts.app', ['title' => 'Struk Digital — ' . $order->order_code])

@section('content')
<div class="w-full min-h-screen bg-[#f0f2f5] py-8 px-4 flex flex-col items-center justify-center print:p-0 print:bg-white">

    <div class="w-full max-w-lg mb-4 print:hidden flex items-center justify-between">
        <a href="{{ route('orders.show', $order->id) }}" class="text-xs font-bold text-[#5b5c61] hover:text-[#ed1e28] flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Status Antrean</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('profile.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                Biodata & Pengeluaran
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-[#ed1e28] text-white font-bold text-xs rounded-xl shadow-xs hover:bg-[#bb0016] transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / PDF</span>
            </button>
        </div>
    </div>

    <!-- Digital Receipt Card -->
    <div class="w-full max-w-lg bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-xl relative overflow-hidden space-y-5 print:shadow-none print:border-none print:p-0">
        
        <!-- Header -->
        <div class="text-center pb-4 border-b border-dashed border-gray-300">
            <div class="w-14 h-14 rounded-2xl mx-auto mb-2 overflow-hidden bg-white p-1 border border-gray-100 shadow-2xs flex items-center justify-center">
                <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Tel-U" class="w-full h-full object-contain">
            </div>
            <h1 class="text-sm sm:text-base font-black text-[#1a1c1d] tracking-wider uppercase">SMPEK KANTIN TELKOM UNIVERSITY</h1>
            <p class="text-xs text-[#5b5c61]">Sistem Antrean & Pemesanan Digital Terpadu</p>
            <div class="mt-2.5 inline-block bg-emerald-50 border border-emerald-200 text-emerald-700 px-3.5 py-1 rounded-full text-xs font-black uppercase tracking-wider">
                ✓ BUKTI TRANSAKSI LUNAS (QRIS)
            </div>
        </div>

        <!-- Meta Details -->
        <div class="grid grid-cols-2 gap-3 text-xs text-[#5b5c61] pb-4 border-b border-dashed border-gray-300">
            <div>
                <span class="text-gray-400 block text-[10px] uppercase">ID TRANSAKSI</span>
                <span class="font-bold text-[#1a1c1d]">{{ $order->order_code }}</span>
            </div>
            <div class="text-right">
                <span class="text-gray-400 block text-[10px] uppercase">WAKTU TRANSAKSI</span>
                <span class="font-bold text-[#1a1c1d]">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[10px] uppercase">PEMBELI</span>
                <span class="font-bold text-[#1a1c1d]">{{ $order->user->name }}</span>
            </div>
            <div class="text-right">
                <span class="text-gray-400 block text-[10px] uppercase">STAN KANTIN</span>
                <span class="font-bold text-[#ed1e28]">{{ $order->stall->name }}</span>
            </div>
        </div>

        <!-- Queue Callout -->
        <div class="bg-rose-50/70 rounded-2xl p-4 text-center border border-rose-100">
            <div class="text-[10px] font-black text-[#5b5c61] uppercase tracking-wider">NOMOR ANTREAN DIGITAL</div>
            <div class="text-4xl font-black text-[#ed1e28] my-1">{{ $order->queue_number ?? $order->order_code }}</div>
            <div class="text-xs text-[#5b5c61]">{{ $order->stall->location }}</div>
        </div>

        <!-- Items Table -->
        <div class="space-y-2.5 pb-4 border-b border-dashed border-gray-300">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Rincian Menu Hidangan</div>
            @foreach($order->items as $item)
                <div class="flex items-start justify-between text-xs">
                    <div>
                        <div class="font-bold text-[#1a1c1d]">{{ $item->quantity }}x {{ $item->menu_name }}</div>
                        <div class="text-[10px] text-gray-400">
                            @if($item->portion) {{ $item->portion }} @endif
                            @if($item->spice_level) • {{ $item->spice_level }} @endif
                            @if($item->notes) • Note: {{ $item->notes }} @endif
                        </div>
                    </div>
                    <div class="font-bold text-[#1a1c1d] shrink-0">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Price Breakdown -->
        <div class="space-y-1.5 text-xs text-[#5b5c61] pb-4 border-b border-dashed border-gray-300">
            <div class="flex items-center justify-between">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span>Biaya Layanan Kampus</span>
                <span>Rp {{ number_format($order->service_fee, 0, ',', '.') }}</span>
            </div>
            @if($order->discount > 0)
                <div class="flex items-center justify-between text-emerald-600 font-bold">
                    <span>Diskon Promo Tel-U</span>
                    <span>-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-sm font-black text-[#1a1c1d]">
                <span>TOTAL AKHIR</span>
                <span class="text-base text-[#ed1e28]">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer Notice -->
        <div class="text-center text-[11px] text-[#5b5c61] space-y-1">
            <p>Terima kasih telah memesan melalui Sistem Kantin Digital Tel-U!</p>
            <p class="text-[10px] text-gray-400">Simpan bukti digital ini dan tunjukkan nomor antrean kepada pihak penjual.</p>
        </div>

    </div>

</div>
@endsection
