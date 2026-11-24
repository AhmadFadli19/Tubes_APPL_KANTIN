@extends('layouts.merchant')

@section('title', 'Pengaturan Stan & Profil Akun — Merchant Hub')
@section('merchant_title', 'Pengaturan Stan')

@section('merchant_content')
<div class="px-4 sm:px-6 lg:px-8 py-5 max-w-7xl mx-auto space-y-6">

    <!-- MAIN SETTINGS FORM (Wraps both Desktop and Mobile elements) -->
    <form id="stallSettingsForm" method="POST" action="{{ route('merchant.stall.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Hidden Global File Inputs (Triggered by labeled buttons anywhere) -->
        <input type="file" id="stallPhotoInput" name="image" accept="image/*" class="hidden" onchange="previewStallPhoto(this)">
        <input type="file" id="ownerAvatarInput" name="avatar" accept="image/*" class="hidden" onchange="previewOwnerAvatar(this)">

        <!-- DESKTOP TOP BREADCRUMB & SAVE ACTIONS -->
        <div class="hidden sm:flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-gray-100">
            <div>
                <div class="text-[10px] font-black text-gray-400 tracking-wider uppercase flex items-center gap-1.5">
                    <span>PORTAL MERCHANT</span>
                    <span>›</span>
                    <span class="text-[#ed1e28]">{{ strtoupper($stall->location ?? 'GEDUNG CACUK R.') }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-[#1a1c1d] tracking-tight mt-0.5">Pengaturan Stan & Biodata Akun</h1>
                <p class="text-xs text-gray-500 mt-0.5">Kelola informasi stan, jam operasional kantin, rekening QRIS, serta profil pemilik akun.</p>
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" onclick="openStallProfileModal()" class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-[#ed1e28] border border-red-200/60 text-xs font-bold rounded-2xl shadow-2xs transition flex items-center gap-2 active:scale-95">
                    <span>✏️</span>
                    <span>Edit Profil Stan</span>
                </button>
                <button type="submit" class="px-5 py-2.5 bg-[#ed1e28] hover:bg-[#bb0016] text-white text-xs font-bold rounded-2xl shadow-sm hover:shadow active:scale-95 transition flex items-center gap-2">
                    <span>💾</span>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </div>

        <!-- MOBILE TOP PROFILE CARD (Matching Reference Screenshot) -->
        <div class="sm:hidden bg-white rounded-3xl p-4 border border-gray-100 shadow-2xs space-y-3">
            <div class="flex items-center gap-3.5">
                <div class="relative w-16 h-16 rounded-2xl overflow-hidden bg-gray-100 border border-gray-100 shrink-0">
                    <img id="mobileStallImagePreview" src="{{ asset($stall->image ?? 'assets/figma/6a55e.png') }}" alt="{{ $stall->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                    <button type="button" onclick="openStallProfileModal()" class="absolute bottom-1 right-1 w-5 h-5 bg-[#ed1e28] text-white rounded-full flex items-center justify-center text-[10px] cursor-pointer shadow-xs active:scale-95" title="Ganti Foto Stan">
                        📷
                    </button>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[9px] font-bold rounded-full border border-emerald-100">
                        <span>✓</span>
                        <span>Terverifikasi Mitra Tel-U</span>
                    </div>
                    <h2 class="text-sm font-black text-[#1a1c1d] truncate mt-0.5">{{ $stall->name }}</h2>
                    <p class="text-[10px] text-gray-400 truncate">{{ $stall->code ?? 'Stan A-03' }} • {{ $stall->location }}</p>
                    <button type="button" onclick="openStallProfileModal()" class="mt-1 text-[11px] font-bold text-[#ed1e28] flex items-center gap-1 hover:underline">
                        <span>✏️ Edit Profil</span>
                    </button>
                </div>
            </div>

            <div class="pt-2 border-t border-gray-100 flex items-center gap-2">
                <button type="button" onclick="openStallProfileModal()" class="flex-1 py-1.5 bg-red-50 text-[#ed1e28] text-center text-xs font-bold rounded-xl cursor-pointer hover:bg-red-100 transition active:scale-95">
                    ✏️ Edit Profil Stan
                </button>
                <a href="{{ route('profile.index', ['tab' => 'biodata']) }}" class="flex-1 py-1.5 bg-gray-100 text-gray-700 text-center text-xs font-bold rounded-xl cursor-pointer hover:bg-gray-200 transition active:scale-95">
                    👤 Edit Biodata Akun
                </a>
            </div>
        </div>

        <!-- NAVIGATION TABS PILLS -->
        <div class="flex items-center gap-2 border-b border-gray-200/70 pb-3 overflow-x-auto no-scrollbar">
            <button type="button" onclick="switchSettingsTab('stall-info')" id="btn-tab-stall-info" class="tab-btn px-4 py-2 bg-[#ed1e28] text-white rounded-2xl text-xs font-bold shadow-2xs whitespace-nowrap transition">
                Profil & Lokasi Stan
            </button>
            <button type="button" onclick="switchSettingsTab('ops-sla')" id="btn-tab-ops-sla" class="tab-btn px-4 py-2 bg-gray-100 text-gray-600 rounded-2xl text-xs font-bold hover:bg-gray-200 transition whitespace-nowrap">
                Operasional & SLA Dapur
            </button>
            <button type="button" onclick="switchSettingsTab('rekening-qris')" id="btn-tab-rekening-qris" class="tab-btn px-4 py-2 bg-gray-100 text-gray-600 rounded-2xl text-xs font-bold hover:bg-gray-200 transition whitespace-nowrap">
                Rekening & QRIS Stan
            </button>
            <button type="button" onclick="switchSettingsTab('biodata-akun')" id="btn-tab-biodata-akun" class="tab-btn px-4 py-2 bg-gray-100 text-gray-600 rounded-2xl text-xs font-bold hover:bg-gray-200 transition whitespace-nowrap">
                Biodata Akun Pemilik
            </button>
        </div>

        <!-- Hidden Fallback Fields for Stall Info in Main Form -->
        <input type="hidden" name="name" value="{{ $stall->name }}">
        <input type="hidden" name="code" value="{{ $stall->code }}">
        <input type="hidden" name="location" value="{{ $stall->location }}">
        <input type="hidden" name="contact_phone" value="{{ $stall->contact_phone }}">
        <input type="hidden" name="culinary_category" value="{{ $stall->culinary_category }}">
        <input type="hidden" name="description" value="{{ $stall->description }}">

        <!-- TAB 1: INFORMASI UTAMA & LOKASI STAN (Compact Space-Efficient Summary) -->
        <div id="section-stall-info" class="settings-section space-y-5">
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-2xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <span class="text-base">🏪</span>
                        <div>
                            <h3 class="text-xs sm:text-sm font-black text-[#1a1c1d]">Informasi Utama Stan Kampus</h3>
                            <p class="text-[10px] text-gray-400">Data stan resmi terdaftar pada database sistem Kantin Telkom University</p>
                        </div>
                    </div>
                    <button type="button" onclick="openStallProfileModal()" class="px-3.5 py-1.5 bg-red-50 hover:bg-red-100 text-[#ed1e28] text-xs font-bold rounded-xl transition flex items-center gap-1.5 active:scale-95 shadow-2xs">
                        <span>✏️ Edit Profil Stan</span>
                    </button>
                </div>

                <!-- Summary Info Cards (Matching Design Mockup) -->
                <div class="space-y-3">
                    <div class="p-3.5 bg-gray-50/80 rounded-2xl border border-gray-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold block uppercase tracking-wider">Nama Stan Makanan</span>
                            <span class="text-sm font-black text-[#1a1c1d]">{{ $stall->name }}</span>
                        </div>
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-100">Tersimpan</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-3 bg-gray-50/80 rounded-2xl border border-gray-100">
                            <span class="text-[10px] text-gray-400 font-bold block uppercase tracking-wider">Kategori Kuliner</span>
                            <span class="text-xs font-bold text-gray-800">{{ $stall->culinary_category ?? 'Makanan Berat' }}</span>
                        </div>
                        <div class="p-3 bg-gray-50/80 rounded-2xl border border-gray-100">
                            <span class="text-[10px] text-gray-400 font-bold block uppercase tracking-wider">Pemilik Stan</span>
                            <span class="text-xs font-bold text-gray-800">{{ $stall->user->name ?? 'Mitra Penjual' }}</span>
                        </div>
                    </div>

                    <div class="p-3.5 bg-gray-50/80 rounded-2xl border border-gray-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold block uppercase tracking-wider">No. WhatsApp Pesanan</span>
                            <span class="text-xs font-black text-gray-800">{{ $stall->contact_phone }}</span>
                        </div>
                        <button type="button" onclick="openStallProfileModal()" class="text-xs font-bold text-[#ed1e28] hover:underline">
                            Ubah
                        </button>
                    </div>

                    <div class="p-3.5 bg-gray-50/80 rounded-2xl border border-gray-100 space-y-1">
                        <span class="text-[10px] text-gray-400 font-bold block uppercase tracking-wider">Lokasi Terdaftar</span>
                        <div class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                            <span class="text-red-500">📍</span>
                            <span>{{ $stall->location }} (Kode: {{ $stall->code ?? '-' }})</span>
                        </div>
                    </div>

                    @if($stall->description)
                        <div class="p-3.5 bg-gray-50/80 rounded-2xl border border-gray-100 space-y-1">
                            <span class="text-[10px] text-gray-400 font-bold block uppercase tracking-wider">Deskripsi & Catatan Stan</span>
                            <p class="text-xs text-gray-600 leading-relaxed">{{ $stall->description }}</p>
                        </div>
                    @endif
                </div>

                <div class="pt-2">
                    <button type="button" onclick="openStallProfileModal()" class="w-full py-2.5 bg-red-50 hover:bg-red-100 text-[#ed1e28] text-xs font-bold rounded-2xl transition flex items-center justify-center gap-2 active:scale-95">
                        <span>✏️ Buka Modal Edit Profil & Lokasi Stan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 2: OPERASIONAL & SLA DAPUR -->
        <div id="section-ops-sla" class="settings-section space-y-5 hidden">
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-2xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="text-base">⏱️</span>
                        <div>
                            <h3 class="text-xs sm:text-sm font-black text-[#1a1c1d]">Operasional & SLA Dapur</h3>
                            <p class="text-[10px] text-gray-400">Atur ketersediaan stan, waktu estimasi masak, dan kuota antrean aktif</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-full">
                        {{ ($stall->is_open ?? true) ? 'Stan Sedang Buka' : 'Stan Sedang Tutup' }}
                    </span>
                </div>

                <!-- Jam Operasional & Status Buka -->
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200/80 flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="text-xs font-bold text-gray-800">Status Toko / Stan Buka</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">Aktifkan untuk menerima pesanan baru dari mahasiswa</div>
                        <input type="text" name="opening_hours" value="{{ old('opening_hours', $stall->opening_hours ?? '07:30 - 17:00 WIB') }}" class="mt-2 w-full max-w-xs px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-700">
                    </div>
                    <label class="inline-flex items-center cursor-pointer select-none shrink-0">
                        <input type="checkbox" name="is_open" value="1" class="sr-only peer" {{ ($stall->is_open ?? true) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 relative"></div>
                    </label>
                </div>

                <!-- 2 Columns: Estimasi Masak (SLA) & Maks Antrean Aktif -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-700">Estimasi Masak (SLA)</span>
                            <span class="text-red-500">⏱️</span>
                        </div>
                        <div class="flex items-baseline gap-1">
                            <span id="slaVal" class="text-2xl font-black text-[#ed1e28]">{{ $stall->sla_minutes ?: 8 }}</span>
                            <span class="text-xs text-gray-400 font-bold">menit/porsi</span>
                        </div>
                        <input type="hidden" id="slaInput" name="sla_minutes" value="{{ $stall->sla_minutes ?: 8 }}">
                        <div class="flex items-center gap-2 pt-1">
                            <button type="button" onclick="adjustSla(-1)" class="w-8 h-8 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold flex items-center justify-center hover:bg-gray-100 transition shadow-2xs">-</button>
                            <span class="text-[10px] text-gray-400 flex-1 text-center font-bold">Sesuaikan SLA</span>
                            <button type="button" onclick="adjustSla(1)" class="w-8 h-8 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold flex items-center justify-center hover:bg-gray-100 transition shadow-2xs">+</button>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-700">Maks. Antrean Aktif</span>
                            <span class="text-blue-500">👥</span>
                        </div>
                        <div class="flex items-baseline gap-1">
                            <span id="maxQueueVal" class="text-2xl font-black text-gray-800">{{ $stall->max_active_queues ?: 15 }}</span>
                            <span class="text-xs text-gray-400 font-bold">pesanan</span>
                        </div>
                        <input type="hidden" id="maxQueueInput" name="max_active_queues" value="{{ $stall->max_active_queues ?: 15 }}">
                        <div class="flex items-center gap-2 pt-1">
                            <button type="button" onclick="adjustMaxQueue(-1)" class="w-8 h-8 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold flex items-center justify-center hover:bg-gray-100 transition shadow-2xs">-</button>
                            <span class="text-[10px] text-gray-400 flex-1 text-center font-bold">Proteksi Antrean</span>
                            <button type="button" onclick="adjustMaxQueue(1)" class="w-8 h-8 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold flex items-center justify-center hover:bg-gray-100 transition shadow-2xs">+</button>
                        </div>
                    </div>
                </div>

                <!-- Toggles: Auto Accept & Notifikasi Suara -->
                <div class="divide-y divide-gray-100">
                    <div class="flex items-center justify-between py-2.5">
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-500">✓</span>
                            <span class="text-xs font-bold text-gray-700">Terima Otomatis Pesanan QRIS Lunas</span>
                        </div>
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="auto_accept_qris" value="1" class="sr-only peer" {{ ($stall->auto_accept_qris ?? true) ? 'checked' : '' }}>
                            <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500 relative"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-2.5">
                        <div class="flex items-center gap-2">
                            <span class="text-red-500">🔔</span>
                            <span class="text-xs font-bold text-gray-700">Notifikasi Suara Pesanan Baru Masuk</span>
                        </div>
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="sound_notification" value="1" class="sr-only peer" {{ ($stall->sound_notification ?? true) ? 'checked' : '' }}>
                            <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500 relative"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: REKENING & QRIS STAN -->
        <div id="section-rekening-qris" class="settings-section space-y-5 hidden">
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-2xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="text-base">💳</span>
                        <div>
                            <h3 class="text-xs sm:text-sm font-black text-[#1a1c1d]">Rekening Bank & QRIS Stan</h3>
                            <p class="text-[10px] text-gray-400">Pencairan otomatis D+0 hasil penjualan nontunai Tel-U Pay</p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-md">
                        Auto D+0
                    </span>
                </div>

                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border border-gray-200/70 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 bg-red-100 text-[#ed1e28] text-[10px] font-black rounded-md">BM</span>
                            <span class="text-xs font-black text-gray-800">Bank Mandiri / BNI Tel-U</span>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-1">
                            <span>✓</span> Terverifikasi
                        </span>
                    </div>
                    <div class="text-base font-black text-[#1a1c1d] tracking-wider pt-1">
                        <input type="text" name="bank_info" value="{{ old('bank_info', $stall->bank_info ?? '1310-0982-1234') }}" class="w-full bg-transparent border-b border-gray-200 py-1 font-black text-base focus:outline-none focus:border-[#ed1e28]">
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-gray-400 pt-1">
                        <span>Atas Nama: <strong>{{ $stall->user->name ?? 'Ibu Siti Rahayu' }}</strong></span>
                        <span class="font-bold text-gray-500">KCP Tel-U</span>
                    </div>
                </div>

                <a href="{{ route('menu.index') }}" target="_blank" class="w-full py-2.5 bg-red-50 hover:bg-red-100 text-[#ed1e28] text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                    <span>📱</span>
                    <span>Lihat QRIS Stan di Katalog Menu Pembeli</span>
                </a>
            </div>
        </div>

        <!-- TAB 4: BIODATA AKUN PEMILIK (Owner & Staff) -->
        <div id="section-biodata-akun" class="settings-section space-y-5 hidden">
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-2xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <span class="text-base">👤</span>
                        <div>
                            <h3 class="text-xs sm:text-sm font-black text-[#1a1c1d]">Biodata Pemilik Akun Stan</h3>
                            <p class="text-[10px] text-gray-400">Informasi profil pemilik, kontak darurat, serta keamanan kata sandi</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 bg-red-50 text-[#ed1e28] text-[10px] font-bold rounded-full">
                        Pemilik Utama
                    </span>
                </div>

                <!-- Owner Photo Preview and Upload Button -->
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden bg-white border border-gray-200 shrink-0">
                        <img id="ownerAvatarPreview" src="{{ asset($stall->user->avatar ?? 'assets/figma/6a55e.png') }}" alt="{{ $stall->user->name ?? 'Pemilik' }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                    </div>
                    <div class="space-y-1">
                        <label for="ownerAvatarInput" class="cursor-pointer px-3.5 py-1.5 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-xl transition inline-block active:scale-95">
                            📷 Ganti Foto Profil Pemilik
                        </label>
                        <p class="text-[10px] text-gray-400">Maks. 5MB (JPG, PNG, WebP). Ditampilkan di header dan struk belanja.</p>
                    </div>
                </div>

                <!-- Nama & Nomor HP Pemilik -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Nama Lengkap Pemilik Stan</label>
                        <input type="text" name="owner_name" value="{{ old('owner_name', $stall->user->name ?? '') }}" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 font-bold focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Nomor WhatsApp / HP Pribadi</label>
                        <input type="text" name="owner_phone" value="{{ old('owner_phone', $stall->user->phone ?? '') }}" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                    </div>
                </div>

                <!-- Email Terdaftar -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Email Akun Login (Terdaftar)</label>
                    <input type="text" disabled value="{{ $stall->user->email ?? 'mitra@telkomuniversity.ac.id' }}" class="w-full px-3.5 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-xs text-gray-400 cursor-not-allowed">
                    <p class="text-[10px] text-gray-400">Email akun terdaftar langsung oleh Administrator Direktorat Logistik Tel-U</p>
                </div>

                <!-- Ganti Password Akun Penjual -->
                <div class="pt-3 border-t border-gray-100 space-y-2">
                    <label class="block text-xs font-bold text-gray-700">Ganti Kata Sandi (Kosongkan bila tidak ingin mengubah)</label>
                    <input type="password" name="owner_password" placeholder="Masukkan kata sandi baru (min. 6 karakter)" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                </div>
            </div>

            <!-- Staf Anggota Stan Info -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-2xs space-y-3">
                <h4 class="text-xs sm:text-sm font-black text-[#1a1c1d]">Daftar Akun Kasir & Staf Stan</h4>
                <div class="divide-y divide-gray-100 text-xs">
                    @foreach($staffMembers as $staff)
                        <div class="py-2.5 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-xl bg-red-100 text-[#ed1e28] font-bold flex items-center justify-center text-xs">
                                    {{ strtoupper(substr($staff['name'], 0, 1)) }}
                                </span>
                                <div>
                                    <div class="font-bold text-[#1a1c1d]">{{ $staff['name'] }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $staff['role_label'] }}</div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $staff['is_owner'] ? 'bg-red-50 text-[#ed1e28]' : 'bg-gray-100 text-gray-600' }}">
                                {{ $staff['status'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- MOBILE BOTTOM STICKY SAVE BUTTON -->
        <div class="sm:hidden space-y-2 pt-2">
            <button type="submit" class="w-full py-3.5 bg-[#ed1e28] text-white text-xs font-black rounded-2xl shadow-md active:scale-95 transition flex items-center justify-center gap-2">
                <span>💾</span>
                <span>Simpan Pengaturan</span>
            </button>
            <button type="button" onclick="location.reload()" class="w-full py-2 text-center text-xs text-gray-400 hover:text-gray-600 font-bold">
                Batalkan Perubahan
            </button>
        </div>

    </form>

    <!-- Modal Edit Profil & Lokasi Stan Penjual (Space-Efficient Modal) -->
    <div id="stallProfileModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-xl w-full p-5 sm:p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto space-y-4 relative">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-red-50 text-[#ed1e28] flex items-center justify-center text-sm font-black">
                        🏪
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-[#1a1c1d]">Edit Profil & Lokasi Stan</h3>
                        <p class="text-[11px] text-[#5b5c61]">Perbarui nama stan, foto/banner, kode, lokasi, WhatsApp, dan deskripsi.</p>
                    </div>
                </div>
                <button type="button" onclick="closeStallProfileModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition active:scale-95" title="Tutup">
                    ✕
                </button>
            </div>

            <form method="POST" action="{{ route('merchant.stall.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="opening_hours" value="{{ $stall->opening_hours ?? '07:30 - 17:00 WIB' }}">

                <!-- Foto Banner Tampilan Stan -->
                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1.5">Foto Banner Tampilan Stan</label>
                    <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-2xl border border-gray-200/80">
                        <div class="w-16 h-16 rounded-2xl overflow-hidden bg-gray-100 border border-gray-200 shrink-0">
                            <img id="modalStallImagePreview" src="{{ asset($stall->image ?? 'assets/figma/6a55e.png') }}" alt="{{ $stall->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                        </div>
                        <div class="space-y-1">
                            <label class="cursor-pointer px-3.5 py-1.5 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-xl transition inline-block active:scale-95 shadow-2xs">
                                📷 Pilih Foto Stan Baru
                                <input type="file" name="image" accept="image/*" class="hidden" onchange="previewModalStallPhoto(this)">
                            </label>
                            <p class="text-[10px] text-gray-400">Format: JPG, PNG, atau WebP (Maks. 5MB).</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Nama Stan Resmi <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name', $stall->name) }}" class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] font-bold focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Kode Stan</label>
                        <input type="text" name="code" value="{{ old('code', $stall->code) }}" placeholder="A-01" class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Lokasi Terdaftar di Area Kantin <span class="text-red-500">*</span></label>
                    <input type="text" name="location" required value="{{ old('location', $stall->location) }}" class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition" placeholder="Contoh: Lantai 1, Blok A Kantin GKU Tel-U">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">No. WhatsApp Pesanan <span class="text-red-500">*</span></label>
                        <input type="text" name="contact_phone" required value="{{ old('contact_phone', $stall->contact_phone) }}" class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition" placeholder="08123456789">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Kategori Menu Utama</label>
                        <input type="text" name="culinary_category" value="{{ old('culinary_category', $stall->culinary_category ?? 'Makanan Berat & Aneka Nasi') }}" class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Deskripsi & Spesialisasi Stan</label>
                    <textarea name="description" rows="3" class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition" placeholder="Catatan keunggulan kuliner stan...">{{ old('description', $stall->description) }}</textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeStallProfileModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#ed1e28] hover:bg-[#d41720] text-white text-xs font-bold rounded-xl shadow-sm transition active:scale-95">
                        Simpan Profil Stan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function openStallProfileModal() {
        const modal = document.getElementById('stallProfileModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeStallProfileModal() {
        const modal = document.getElementById('stallProfileModal');
        if (modal) modal.classList.add('hidden');
    }

    function previewModalStallPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const m = document.getElementById('modalStallImagePreview');
                const mob = document.getElementById('mobileStallImagePreview');
                if (m) m.src = e.target.result;
                if (mob) mob.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeStallProfileModal();
    });
    const stallModal = document.getElementById('stallProfileModal');
    if (stallModal) {
        stallModal.addEventListener('click', function(e) {
            if (e.target === this) closeStallProfileModal();
        });
    }

    function previewStallPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const desk = document.getElementById('desktopStallImagePreview');
                const mob = document.getElementById('mobileStallImagePreview');
                if (desk) desk.src = e.target.result;
                if (mob) mob.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewOwnerAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const av = document.getElementById('ownerAvatarPreview');
                if (av) av.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function switchSettingsTab(tabName) {
        // Hide all sections
        const sections = document.querySelectorAll('.settings-section');
        sections.forEach(s => s.classList.add('hidden'));

        // Show target section
        const target = document.getElementById('section-' + tabName);
        if (target) target.classList.remove('hidden');

        // Update button states
        const buttons = document.querySelectorAll('.tab-btn');
        buttons.forEach(b => {
            b.classList.remove('bg-[#ed1e28]', 'text-white', 'shadow-2xs');
            b.classList.add('bg-gray-100', 'text-gray-600');
        });

        const activeBtn = document.getElementById('btn-tab-' + tabName);
        if (activeBtn) {
            activeBtn.classList.remove('bg-gray-100', 'text-gray-600');
            activeBtn.classList.add('bg-[#ed1e28]', 'text-white', 'shadow-2xs');
        }
    }

    function adjustSla(delta) {
        const inp = document.getElementById('slaInput');
        const val = document.getElementById('slaVal');
        let current = parseInt(inp.value || 8);
        let newVal = Math.max(1, current + delta);
        inp.value = newVal;
        val.innerText = newVal;
    }

    function adjustMaxQueue(delta) {
        const inp = document.getElementById('maxQueueInput');
        const val = document.getElementById('maxQueueVal');
        let current = parseInt(inp.value || 15);
        let newVal = Math.max(1, current + delta);
        inp.value = newVal;
        val.innerText = newVal;
    }
</script>
@endsection
