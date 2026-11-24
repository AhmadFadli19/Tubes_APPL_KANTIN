@extends('layouts.app', ['title' => 'Kantin Tel-U — Masuk Akun'])

@section('content')
<div class="w-full min-h-screen flex items-center justify-center p-3 sm:p-6 lg:p-10 bg-[#f0f2f5]">

    <div class="w-full max-w-md lg:max-w-4xl bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col lg:flex-row">
        
        <!-- Left Side: Campus Branding & Hero Showcase (Visible on Desktop) -->
        <div class="hidden lg:flex lg:w-5/12 bg-gradient-to-br from-[#ed1e28] via-[#c6151e] to-[#800000] text-white p-8 flex-col justify-between relative overflow-hidden">
            <!-- Ambient Glow Effects -->
            <div class="absolute -top-16 -right-16 w-56 h-56 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-black/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 space-y-6">
                <!-- Tel-U Brand Header -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white p-2 shadow-md flex items-center justify-center shrink-0">
                        <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Tel-U Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="text-[10px] font-black tracking-widest text-red-200 uppercase">KANTIN TERPADU</div>
                        <div class="text-base font-black tracking-tight leading-tight">TELKOM UNIVERSITY</div>
                    </div>
                </div>

                <!-- Hero Canteen Visual Card (Matching Figma Showcase) -->
                <div class="w-full h-44 rounded-2xl overflow-hidden shadow-lg border border-white/20 relative bg-black/20 group">
                    <img src="{{ asset('assets/figma/7e0d3.png') }}" alt="Kantin Telkom University" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>
                    
                    <!-- Live Stall Status Badge -->
                    <div class="absolute top-2.5 left-2.5 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full text-[10px] font-bold text-gray-800 shadow-sm flex items-center gap-1.5 border border-white/60">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>32+ Stan Kuliner Aktif</span>
                    </div>

                    <!-- Campus Location Label -->
                    <div class="absolute bottom-2.5 left-2.5 right-2.5 text-[11px] text-white/95 font-semibold flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                        <span class="truncate">Gedung Tokong Nanas (GKU) & Selaru</span>
                    </div>
                </div>

                <!-- Headline & Value Proposition -->
                <div class="space-y-2">
                    <h2 class="text-xl font-black leading-snug">Pesan Makanan Tanpa Harus Antre Berdesakan.</h2>
                    <p class="text-white/80 text-xs leading-relaxed">
                        Nikmati kemudahan memesan makanan lezat khas kantin Telkom University langsung dari gawai Anda secara real-time.
                    </p>
                </div>
            </div>

            <!-- Features Quick Badges -->
            <div class="relative z-10 pt-4 border-t border-white/15 space-y-2.5 text-xs text-white/90">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center shrink-0 text-white font-bold text-[11px]">⚡</div>
                    <span>Nomor antrean otomatis & estimasi matang</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center shrink-0 text-white font-bold text-[11px]">💳</div>
                    <span>Pembayaran praktis QRIS Tel-U Pay</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center shrink-0 text-white font-bold text-[11px]">🔔</div>
                    <span>Notifikasi siap ambil di etalase stan</span>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="relative z-10 pt-4 text-[10px] text-white/60 tracking-wider uppercase font-semibold">
                Sistem Pemesanan Digital &bull; Telkom University
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-7/12 p-6 sm:p-8 lg:p-10 flex flex-col justify-between bg-[#faf9fb]">
            <div>
                <!-- Top Navigation Header -->
                <div class="flex items-center justify-between mb-5">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[#5b5c61] hover:text-[#1a1c1d] transition">
                        <img src="{{ asset('assets/figma/76355.svg') }}" alt="Kembali" class="w-3.5 h-3.5">
                        <span>Beranda</span>
                    </a>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-700">Kantin Tel-U</span>
                        <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Logo" class="w-6 h-6 object-contain">
                    </div>
                </div>

                <!-- Mobile Hero Illustration Banner (Visible only on mobile/tablet) -->
                <div class="lg:hidden mb-5 rounded-2xl overflow-hidden shadow-sm border border-red-100 relative bg-gradient-to-r from-[#ed1e28] to-[#990000] p-4 text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white p-1.5 shadow-md flex items-center justify-center shrink-0">
                            <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Tel-U" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <div class="text-[10px] font-black uppercase tracking-widest text-red-200">Kantin Terpadu</div>
                            <div class="text-sm font-black leading-tight">Telkom University</div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center gap-2 text-[11px] text-white/90 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                        <span>Pesan menu favorit & ambil tanpa antre!</span>
                    </div>
                </div>

                <!-- Greeting -->
                <div class="mb-5">
                    <h1 class="text-2xl font-black text-[#1a1c1d] tracking-tight">Selamat Datang Kembali! 👋</h1>
                    <p class="text-xs text-[#5b5c61] mt-1">
                        Masuk ke akun civitas Tel-U Anda untuk memesan menu atau mengelola operasional stan kantin.
                    </p>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="identifierInput" class="block text-xs font-bold text-[#1a1c1d] mb-1.5">Email atau Username Civitas</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text" id="identifierInput" name="identifier" value="{{ old('identifier') }}" required autofocus
                                class="w-full bg-white border border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-xs text-[#1a1c1d] focus:outline-none focus:border-[#ed1e28] focus:ring-2 focus:ring-[#ed1e28]/10 transition shadow-2xs"
                                placeholder="nama@telkomuniversity.ac.id / username">
                        </div>
                        @error('identifier')
                            <p class="text-[11px] text-red-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="passwordInput" class="block text-xs font-bold text-[#1a1c1d]">Kata Sandi</label>
                            <button type="button" onclick="openForgotPasswordModal()" class="text-[11px] text-[#ed1e28] hover:text-[#b6252a] font-semibold transition">
                                Lupa Sandi?
                            </button>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input type="password" id="passwordInput" name="password" required
                                class="w-full bg-white border border-gray-200 rounded-2xl pl-10 pr-10 py-3 text-xs text-[#1a1c1d] focus:outline-none focus:border-[#ed1e28] focus:ring-2 focus:ring-[#ed1e28]/10 transition shadow-2xs"
                                placeholder="Masukkan kata sandi akun Anda">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" title="Tampilkan / Sembunyikan Kata Sandi">
                                <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-[11px] text-red-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-[#ed1e28] rounded border-gray-300 focus:ring-[#ed1e28]">
                            <span class="text-xs text-[#5b5c61]">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-[#ed1e28] to-[#d41720] hover:from-[#d41720] hover:to-[#b6252a] text-white font-black text-xs py-3.5 rounded-2xl shadow-lg shadow-red-500/25 active:scale-[0.99] transition flex items-center justify-center gap-2">
                        <span>Masuk ke Akun</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>

            <!-- Footer links -->
            <div class="mt-8 pt-4 border-t border-gray-100 text-center space-y-2">
                <p class="text-xs text-[#5b5c61]">
                    Belum punya akun civitas?
                    <a href="{{ route('register') }}" class="font-bold text-[#ed1e28] hover:underline">Daftar Akun Baru</a>
                </p>
                <p class="text-[11px] text-gray-500">
                    Ingin membuka stan kuliner di kantin? 
                    <a href="{{ route('seller.register.form') }}" class="font-semibold text-gray-700 hover:text-[#ed1e28] underline">Daftar Mitra Penjual</a>
                </p>
            </div>
        </div>

    </div>

</div>

<!-- Forgot Password Modal -->
<div id="forgotPasswordModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-gray-100 space-y-4">
        <div class="w-12 h-12 rounded-2xl bg-red-50 text-[#ed1e28] flex items-center justify-center mx-auto text-xl">
            🔑
        </div>
        <div class="text-center">
            <h3 class="text-base font-black text-[#1a1c1d]">Bantuan Reset Kata Sandi</h3>
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                Untuk keamanan akun civitas Anda, reset kata sandi dikelola langsung oleh administrator Direktorat Logistik & Pengelola Kantin Telkom University.
            </p>
        </div>
        <div class="bg-gray-50 rounded-2xl p-3 text-xs text-gray-700 space-y-1.5 border border-gray-200">
            <div class="flex items-center gap-2">
                <span class="font-bold">📧 Email:</span>
                <span class="font-mono text-gray-600">admin@telkomuniversity.ac.id</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-bold">🏢 Lokasi:</span>
                <span>Gedung GKU Lantai 1 / Selaru</span>
            </div>
        </div>
        <button type="button" onclick="closeForgotPasswordModal()" class="w-full py-2.5 bg-[#ed1e28] hover:bg-[#d41720] text-white text-xs font-bold rounded-xl shadow-md transition">
            Tutup
        </button>
    </div>
</div>

@push('scripts')
<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    }

    function openForgotPasswordModal() {
        document.getElementById('forgotPasswordModal').classList.remove('hidden');
    }

    function closeForgotPasswordModal() {
        document.getElementById('forgotPasswordModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
