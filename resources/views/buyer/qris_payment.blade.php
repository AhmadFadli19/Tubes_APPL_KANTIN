@extends('layouts.app', ['title' => 'Scan QRIS Tel-U Pay — ' . $order->order_code])

@section('content')
<div class="w-full min-h-screen bg-[#f0f2f5] py-8 pb-24 md:py-8 px-4 flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col justify-between">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#ed1e28] to-[#990000] text-white p-6 relative overflow-hidden text-center">
            <div class="flex items-center justify-between mb-3">
                <a href="{{ route('cart.index') }}" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <span class="text-[10px] font-black uppercase tracking-widest text-red-200">PEMBAYARAN RESMI KAMPUS</span>
                <div class="w-8"></div>
            </div>

            <div class="w-12 h-12 rounded-2xl bg-white p-1.5 mx-auto mb-2 shadow-md flex items-center justify-center">
                <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Logo Tel-U" class="w-full h-full object-contain">
            </div>

            <h1 class="text-base sm:text-lg font-black tracking-tight">QRIS Nontunai Tel-U Pay</h1>
            <p class="text-[11px] text-white/80 mt-0.5">Stan: <strong>{{ $order->stall->name }}</strong> • Kode: {{ $order->order_code }}</p>
        </div>

        <!-- Body: QRIS Code & Instructions -->
        <div class="p-6 text-center space-y-4">
            
            <div>
                <div class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">TOTAL TAGIHAN</div>
                <div class="text-2xl sm:text-3xl font-black text-[#ed1e28] mt-0.5">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </div>
            </div>

            <!-- Dynamic QR Code Container -->
            <div class="relative bg-white p-4 rounded-3xl border-2 border-dashed border-red-200 inline-block mx-auto shadow-inner group">
                <!-- QR Code Image generated dynamically with target pay URL -->
                <img 
                    id="qrisImage"
                    src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($payUrl) }}&margin=10" 
                    alt="QRIS Tel-U Pay" 
                    class="w-60 h-60 sm:w-64 sm:h-64 object-contain mx-auto rounded-xl"
                    onerror="this.src='https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={{ urlencode($payUrl) }}&choe=UTF-8'"
                >

                <!-- Center Logo Overlay -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-12 h-12 rounded-xl bg-white p-1 shadow-md border border-gray-200 flex items-center justify-center">
                        <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Tel-U" class="w-full h-full object-contain">
                    </div>
                </div>
            </div>

            <!-- Google Lens Prompt Banner -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-3.5 text-left flex items-start gap-3">
                <div class="w-7 h-7 rounded-xl bg-amber-500 text-white flex items-center justify-center text-sm font-bold shrink-0 mt-0.5">
                    🔍
                </div>
                <div>
                    <h3 class="text-xs font-black text-amber-950">Scan Pakai Google Lens / Kamera HP</h3>
                    <p class="text-[11px] text-amber-900 mt-0.5 leading-relaxed">
                        Arahkan Google Lens HP Anda ke QR di atas, lalu <strong>klik link yang muncul</strong>. Tagihan akan langsung otomatis terbayar dan nomor antrean diterbitkan seketika!
                    </p>
                </div>
            </div>

            <!-- Direct Simulation Button (Klik Langsung di PC) -->
            <div class="space-y-2 pt-2">
                <a href="{{ $localPayUrl }}" class="w-full bg-[#ed1e28] hover:bg-[#d41720] text-white font-black text-xs py-3 rounded-2xl shadow-lg shadow-red-500/25 active:scale-95 transition flex items-center justify-center gap-2">
                    <span>⚡ Klik di Sini untuk Bayar Langsung (Simulasi)</span>
                </a>
                <p class="text-[10px] text-gray-400">
                    Menunggu scan HP... Halaman ini akan otomatis beralih saat pembayaran terdeteksi.
                </p>
            </div>

        </div>

        <!-- Footer -->
        <div class="p-4 bg-gray-50 border-t border-gray-100 text-center text-[11px] text-gray-500 flex items-center justify-between">
            <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span>Polling Pembayaran Aktif</span>
            </span>
            <span class="font-bold text-gray-700">QRIS GPN Tel-U</span>
        </div>

    </div>

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
    // Real-time polling to check if phone has scanned and completed payment
    const orderId = {{ $order->id }};
    const checkStatusUrl = '{{ route("orders.status.api", $order->id) }}';
    const redirectUrl = '{{ route("orders.show", $order->id) }}';

    const interval = setInterval(async () => {
        try {
            const res = await fetch(checkStatusUrl);
            if (!res.ok) return;
            const data = await res.json();

            if (data.is_paid || data.status === 'diproses' || data.status === 'siap_diambil' || data.status === 'selesai') {
                clearInterval(interval);
                // Cheerful notification audio chime
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = audioCtx.createOscillator();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                    osc.connect(audioCtx.destination);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.3);
                } catch(e) {}

                // Immediate redirect to "Sudah Bayar" screen!
                window.location.href = redirectUrl + '?paid=1';
            }
        } catch (err) {
            console.error('Polling error:', err);
        }
    }, 2000);
</script>
@endpush
@endsection
