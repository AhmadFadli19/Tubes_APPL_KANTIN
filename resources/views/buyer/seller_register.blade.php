@extends('layouts.app', ['title' => 'Pendaftaran Mitra Penjual Stan — Kantin Tel-U'])

@section('content')
<div class="w-full min-h-screen bg-[#f0f2f5] py-8 px-4 sm:px-6 lg:px-8 flex items-center justify-center">

    <div class="w-full max-w-3xl bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#ed1e28] via-[#cb111c] to-[#990000] text-white p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white p-2 flex items-center justify-center shadow">
                        <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Logo Tel-U" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-red-200">KEMITRAAN KANTIN TEL-U</span>
                        <h1 class="text-xl sm:text-2xl font-black">Formulir Pendaftaran Stan Penjual</h1>
                    </div>
                </div>
                <a href="{{ route('menu.index') }}" class="px-3 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 text-xs font-bold transition flex items-center gap-1.5 text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali</span>
                </a>
            </div>
            <p class="mt-3 text-xs text-white/90 leading-relaxed max-w-xl">
                Buka stan usaha kuliner Anda di Kantin Terpadu Telkom University. Formulir ini akan langsung ditinjau oleh Administrator Direktorat Logistik kampus.
            </p>
        </div>

        @if(isset($existingRequest) && $existingRequest)
            <div class="p-6 bg-amber-50 border-b border-amber-200">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold shrink-0">!</div>
                    <div>
                        <h4 class="text-xs font-bold text-amber-900">Pengajuan Terakhir Anda:</h4>
                        <p class="text-xs text-amber-800 mt-0.5">
                            Stan <strong>{{ $existingRequest->stall_name }}</strong> diajukan pada {{ $existingRequest->created_at->format('d M Y H:i') }} dengan status:
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $existingRequest->status === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : ($existingRequest->status === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $existingRequest->status }}
                            </span>
                        </p>
                        @if($existingRequest->admin_notes)
                            <p class="text-[11px] text-amber-700 mt-1 italic">Catatan Admin: "{{ $existingRequest->admin_notes }}"</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Body -->
        <form method="POST" action="{{ route('seller.register.submit') }}" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Section 1: Data Pemilik / Pemohon -->
            <div>
                <div class="flex items-center gap-2 pb-2 border-b border-gray-100 mb-4">
                    <span class="w-6 h-6 rounded-full bg-red-100 text-[#ed1e28] text-xs font-bold flex items-center justify-center">1</span>
                    <h2 class="text-sm font-bold text-[#1a1c1d]">Data Pemilik / Penanggung Jawab</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Nama Lengkap Pemilik <span class="text-red-500">*</span></label>
                        <input type="text" name="applicant_name" value="{{ old('applicant_name', auth()->user()->name ?? '') }}" required
                            class="w-full bg-[#faf9fb] border border-gray-200 rounded-2xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"
                            placeholder="Contoh: H. Ismail Marzuki">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Email Aktif (Login) <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required
                            class="w-full bg-[#faf9fb] border border-gray-200 rounded-2xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"
                            placeholder="penjual@gmail.com">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required
                            class="w-full bg-[#faf9fb] border border-gray-200 rounded-2xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"
                            placeholder="08123456789">
                    </div>
                </div>
            </div>

            <!-- Section 2: Biodata Stan & Menu -->
            <div>
                <div class="flex items-center gap-2 pb-2 border-b border-gray-100 mb-4">
                    <span class="w-6 h-6 rounded-full bg-red-100 text-[#ed1e28] text-xs font-bold flex items-center justify-center">2</span>
                    <h2 class="text-sm font-bold text-[#1a1c1d]">Biodata Stan & Rencana Usaha</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Nama Stan Kuliner <span class="text-red-500">*</span></label>
                        <input type="text" name="stall_name" value="{{ old('stall_name') }}" required
                            class="w-full bg-[#faf9fb] border border-gray-200 rounded-2xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"
                            placeholder="Contoh: Soto Ayam Lamongan Cak Har">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Preferensi Lokasi Kantin <span class="text-red-500">*</span></label>
                        <input type="text" name="stall_location" value="{{ old('stall_location', 'Kantin GKU Lantai 1 / Terpadu Selaru') }}" required
                            class="w-full bg-[#faf9fb] border border-gray-200 rounded-2xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"
                            placeholder="Contoh: Lantai 1 Blok B Kantin GKU">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Rencana Jam Operasional <span class="text-red-500">*</span></label>
                        <input type="text" name="opening_hours" value="{{ old('opening_hours', '08:00 - 17:00 WIB') }}" required
                            class="w-full bg-[#faf9fb] border border-gray-200 rounded-2xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"
                            placeholder="Contoh: 08:00 - 17:00 WIB">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Rekening Pembayaran / E-Wallet</label>
                        <input type="text" name="bank_info" value="{{ old('bank_info') }}"
                            class="w-full bg-[#faf9fb] border border-gray-200 rounded-2xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"
                            placeholder="Contoh: BCA 82718291 a.n Nama Pemilik / Tel-U Pay">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Deskripsi Usaha & Menu Utama <span class="text-red-500">*</span></label>
                        <textarea name="stall_description" rows="3" required
                            class="w-full bg-[#faf9fb] border border-gray-200 rounded-2xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"
                            placeholder="Jelaskan jenis makanan/minuman yang akan dijual, keunggulan menu, dan kisaran harga.">{{ old('stall_description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('menu.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-800">
                    Batal
                </a>
                <button type="submit" class="bg-[#ed1e28] hover:bg-[#d41720] text-white text-xs font-bold px-6 py-3 rounded-2xl shadow-lg shadow-red-500/20 active:scale-95 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Kirim Pengajuan Pendaftaran Stan</span>
                </button>
            </div>
        </form>

    </div>

</div>
@endsection
