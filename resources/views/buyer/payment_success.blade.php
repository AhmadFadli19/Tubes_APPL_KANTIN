@extends('layouts.app', ['title' => 'Pembayaran Berhasil — ' . $order->order_code])

@section('content')
<div class="w-full min-h-screen bg-[#f0f2f5] py-8 px-4 flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden text-center">
        
        <!-- Top Green Banner -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white p-6 sm:p-8 relative overflow-hidden">
            <div class="w-16 h-16 rounded-full bg-white/20 text-white text-3xl font-black flex items-center justify-center mx-auto mb-3 shadow-inner">
                ✓
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-200">GOOGLE LENS SCAN SUKSES</span>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight mt-1">Pembayaran QRIS Berhasil!</h1>
            <p class="text-xs text-white/90 mt-1">Tagihan lunas & pesanan langsung masuk ke dapur koki.</p>
        </div>

        <!-- Body: Queue Number & Details -->
        <div class="p-6 sm:p-8 space-y-5">
            
            <!-- Queue Number Card -->
            <div class="bg-rose-50/70 border border-red-100 rounded-3xl p-5">
                <div class="text-[10px] font-black text-gray-500 uppercase tracking-wider">NOMOR ANTREAN ANDA</div>
                <div class="text-5xl font-black text-[#ed1e28] my-2 tracking-tight">
                    {{ $order->queue_number ?? 'A-001' }}
                </div>
                <div class="text-xs font-bold text-gray-800">
                    🏪 {{ $order->stall->name }}
                </div>
                <div class="text-[11px] text-gray-500 mt-0.5">
                    {{ $order->stall->location }}
                </div>
            </div>

            <!-- Transaction Summary -->
            <div class="bg-gray-50 rounded-2xl p-4 text-xs space-y-2 text-left">
                <div class="flex items-center justify-between text-gray-500">
                    <span>Kode Transaksi:</span>
                    <span class="font-bold text-gray-800">{{ $order->order_code }}</span>
                </div>
                <div class="flex items-center justify-between text-gray-500">
                    <span>Metode:</span>
                    <span class="font-bold text-emerald-700">QRIS Nontunai (Lunas)</span>
                </div>
                <div class="flex items-center justify-between text-gray-500">
                    <span>Total Bayar:</span>
                    <span class="font-black text-[#1a1c1d] text-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="pt-2 border-t border-gray-200">
                    <span class="text-[11px] text-gray-500 block mb-1 font-bold">Menu yang Dipesan:</span>
                    <div class="space-y-1">
                        @foreach($order->items as $it)
                            <div class="flex items-center justify-between text-[11px] text-gray-700">
                                <span>{{ $it->quantity }}x {{ $it->menu_name }}</span>
                                <span class="font-semibold">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-3 text-xs text-emerald-800 text-left flex items-center gap-2.5">
                <span class="text-base">🔔</span>
                <span class="text-[11px] leading-tight font-medium">
                    Tidak perlu konfirmasi manual lagi ke kasir! Silakan tunggu panggilan nomor antrean Anda di depan etalase stan.
                </span>
            </div>

            <!-- CTA Actions -->
            <div class="space-y-2 pt-2">
                @auth
                    <a href="{{ route('orders.show', $order->id) }}" class="w-full bg-[#ed1e28] hover:bg-[#d41720] text-white font-black text-xs py-3 rounded-2xl shadow-lg shadow-red-500/20 active:scale-95 transition flex items-center justify-center gap-2">
                        <span>Pantau Antrean Live di Aplikasi</span>
                        <span>→</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full bg-[#1a1c1d] hover:bg-black text-white font-black text-xs py-3 rounded-2xl shadow-md active:scale-95 transition flex items-center justify-center gap-2">
                        <span>Masuk ke Akun untuk Pantau Status</span>
                    </a>
                @endauth
            </div>

        </div>

    </div>

</div>
@endsection
