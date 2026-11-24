@extends('layouts.app', ['title' => 'Kantin Tel-U — Daftar Akun Pembeli'])

@section('content')
<div class="w-full min-h-screen flex items-center justify-center p-3 sm:p-6 lg:p-10 bg-[#f0f2f5]">

    <div class="w-full max-w-md lg:max-w-4xl bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col lg:flex-row">
        
        <!-- Left Side: Campus Branding & Showcase (Visible on Desktop) -->
        <div class="hidden lg:flex lg:w-5/12 bg-gradient-to-br from-[#ed1e28] via-[#cb111c] to-[#990000] text-white p-10 flex-col justify-between relative overflow-hidden">
            <div class="absolute -top-16 -right-16 w-56 h-56 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-black/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-white p-2 shadow-md flex items-center justify-center">
                        <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Tel-U Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="text-xs font-black tracking-widest text-red-200 uppercase">KANTIN TERPADU</div>
                        <div class="text-lg font-black tracking-tight">TELKOM UNIVERSITY</div>
                    </div>
                </div>

                <div class="space-y-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Registrasi Mahasiswa & Civitas
                    </span>
                    <h2 class="text-2xl font-black leading-tight">Buat Akun untuk Memulai Pemesanan Cepat.</h2>
                    <p class="text-white/80 text-xs leading-relaxed">
                        Satu akun untuk seluruh transaksi kantin Telkom University, simpan preferensi porsi, dan dapatkan notifikasi langsung.
                    </p>
                </div>
            </div>

            <div class="relative z-10 space-y-3 my-6 py-6 border-y border-white/15 text-xs text-white/90">
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center shrink-0">1</div>
                    <span>Daftarkan identitas mahasiswa atau civitas</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center shrink-0">2</div>
                    <span>Pilih menu stan favorit & bayar via QRIS</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center shrink-0">3</div>
                    <span>Ambil pesanan saat nomor antrean dipanggil</span>
                </div>
            </div>

            <div class="relative z-10 text-[11px] text-white/70">
                Kantin Terpadu Kampus Tel-U Bandung &copy; {{ date('Y') }}
            </div>
        </div>

        <!-- Right Side: Registration Form -->
        <div class="w-full lg:w-7/12 p-6 sm:p-8 lg:p-10 flex flex-col justify-between bg-[#faf9fb]">
            <div>
                <!-- Top Navigation Header -->
                <div class="flex items-center justify-between mb-6">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[#5b5c61] hover:text-[#1a1c1d] transition">
                        <img src="{{ asset('assets/figma/76355.svg') }}" alt="Kembali" class="w-3.5 h-3.5">
                        <span>Sudah Punya Akun</span>
                    </a>
                    <div class="flex items-center gap-2 lg:hidden">
                        <span class="text-xs font-semibold text-[#5b5c61]">Kantin Tel-U</span>
                        <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Logo" class="w-6 h-6 object-contain">
                    </div>
                </div>

                <!-- Greeting -->
                <div class="mb-5">
                    <h1 class="text-2xl font-black text-[#1a1c1d] tracking-tight">Daftar Akun Baru 📝</h1>
                    <p class="text-xs text-[#5b5c61] mt-1">
                        Isi biodata Anda untuk menikmati kemudahan sistem pemesanan kantin.
                    </p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full bg-white border border-gray-200 rounded-2xl px-4 py-2.5 text-xs text-[#1a1c1d] focus:outline-none focus:border-[#ed1e28] focus:ring-2 focus:ring-[#ed1e28]/10 transition"
                            placeholder="Contoh: Muhammad Farhan Alamsyah">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Email Kampus / Pribadi</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full bg-white border border-gray-200 rounded-2xl px-4 py-2.5 text-xs text-[#1a1c1d] focus:outline-none focus:border-[#ed1e28] focus:ring-2 focus:ring-[#ed1e28]/10 transition"
                                placeholder="nama@student.telkomuniversity.ac.id">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Nomor WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required
                                class="w-full bg-white border border-gray-200 rounded-2xl px-4 py-2.5 text-xs text-[#1a1c1d] focus:outline-none focus:border-[#ed1e28] focus:ring-2 focus:ring-[#ed1e28]/10 transition"
                                placeholder="08123456789">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Kata Sandi</label>
                            <input type="password" name="password" required
                                class="w-full bg-white border border-gray-200 rounded-2xl px-4 py-2.5 text-xs text-[#1a1c1d] focus:outline-none focus:border-[#ed1e28] focus:ring-2 focus:ring-[#ed1e28]/10 transition"
                                placeholder="Min. 6 karakter">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Ulangi Kata Sandi</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full bg-white border border-gray-200 rounded-2xl px-4 py-2.5 text-xs text-[#1a1c1d] focus:outline-none focus:border-[#ed1e28] focus:ring-2 focus:ring-[#ed1e28]/10 transition"
                                placeholder="Ketik ulang kata sandi">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-[#ed1e28] hover:bg-[#d41720] text-white font-bold text-xs py-3 rounded-2xl shadow-lg shadow-red-500/20 active:scale-[0.99] transition flex items-center justify-center gap-2">
                            <span>Selesaikan Pendaftaran</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer links -->
            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <p class="text-xs text-[#5b5c61]">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" class="font-bold text-[#ed1e28] hover:underline">Masuk di sini</a>
                </p>
                <p class="text-[11px] text-gray-400 mt-2">
                    Ingin menjadi mitra penjual stan di kantin? <a href="{{ route('seller.register.form') }}" class="font-semibold text-gray-600 hover:text-[#ed1e28] underline">Daftar Buka Stan</a>
                </p>
            </div>
        </div>

    </div>

</div>
@endsection
