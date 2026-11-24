@extends('layouts.app', ['title' => 'Kantin Tel-U — Biodata & Laporan Pengeluaran'])

@section('content')
<div class="w-full min-h-screen bg-[#faf9fb] flex flex-col justify-between pb-24 md:pb-12">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-2xs px-4 sm:px-8 py-3.5">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                @php
                    $brandRoute = route('home');
                    if ($user->isAdmin()) {
                        $brandRoute = route('admin.dashboard');
                    } elseif ($user->isPenjual()) {
                        $brandRoute = route('merchant.dashboard');
                    }
                @endphp
                <a href="{{ $brandRoute }}" class="flex items-center gap-2.5 shrink-0 hover:opacity-85 transition" title="Kantin Tel-U">
                    <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Tel-U" class="w-9 h-9 sm:w-10 sm:h-10 object-contain drop-shadow-2xs">
                </a>
                <div>
                    <h1 class="text-sm sm:text-base font-black text-[#1a1c1d]">Biodata & Akun Civitas</h1>
                    <div class="text-[11px] text-[#5b5c61]">Informasi profil, riwayat pesanan & laporan pengeluaran</div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($user->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 text-[#ed1e28] hover:bg-red-100 text-xs font-bold transition">
                        <span>🛡️ Panel Admin</span>
                    </a>
                @elseif($user->isPenjual())
                    <a href="{{ route('merchant.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 text-[#ed1e28] hover:bg-red-100 text-xs font-bold transition">
                        <span>🏪 Panel Stan</span>
                    </a>
                @endif

                <!-- Tombol Keluar Langsung di Header -->
                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari akun?');">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-bold transition flex items-center gap-1.5 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 flex-1 space-y-6">

        <!-- User Identity Card -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-red-500/5 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex items-center gap-4">
                <!-- Profile Avatar with Camera Edit Trigger -->
                <div class="relative group shrink-0">
                    <div id="mainAvatarDisplay" onclick="triggerAvatarUpload()" class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl overflow-hidden bg-[#ed1e28] text-white flex items-center justify-center text-2xl sm:text-3xl font-black shadow-lg shadow-red-500/20 border-2 border-white ring-2 ring-gray-100 cursor-pointer active:scale-95 transition" title="Klik untuk ganti foto profil">
                        @if($user->avatar)
                            <img id="mainAvatarImg" src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span id="mainAvatarInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <!-- Camera badge button -->
                    <button type="button" onclick="triggerAvatarUpload()" class="absolute -bottom-1 -right-1 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white text-[#1a1c1d] shadow-md border border-gray-200 flex items-center justify-center hover:bg-red-50 hover:text-[#ed1e28] transition active:scale-95" title="Ganti Foto Profil">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                </div>

                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg sm:text-xl font-black text-[#1a1c1d]">{{ $user->name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : ($user->role === 'penjual' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                            {{ $user->role === 'admin' ? 'Super Admin' : ($user->role === 'penjual' ? 'Mitra Penjual' : 'Pembeli / Civitas') }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-[10px] font-bold text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#2e9e5b]"></span>
                            Aktif
                        </span>
                    </div>
                    <div class="text-xs text-[#5b5c61] mt-1 space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span>📧 {{ $user->email }}</span>
                            <span>•</span>
                            <span>📱 {{ $user->phone ?? 'Belum diisi' }}</span>
                        </div>
                        <div class="text-[11px] text-gray-400">
                            Username: <span class="font-mono font-bold text-gray-600">&#64;{{ $user->username ?? 'user' }}</span> | Terdaftar sejak {{ $user->created_at->format('d M Y') }}
                        </div>
                    </div>
                    <div class="mt-2 flex items-center gap-2">
                        <button type="button" onclick="openBiodataModal()" class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 hover:bg-red-100 text-[#ed1e28] rounded-xl text-xs font-bold transition shadow-2xs active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span>Edit Biodata & Foto</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                @if($user->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex-1 md:flex-initial px-4 py-2.5 bg-[#ed1e28] hover:bg-[#d41720] text-white text-xs font-bold rounded-xl shadow-sm text-center transition">
                        Buka Dashboard Admin
                    </a>
                @elseif($user->isPenjual())
                    <a href="{{ route('merchant.dashboard') }}" class="flex-1 md:flex-initial px-4 py-2.5 bg-[#ed1e28] hover:bg-[#d41720] text-white text-xs font-bold rounded-xl shadow-sm text-center transition">
                        Buka Dashboard Penjual
                    </a>
                @else
                    <a href="{{ route('menu.index') }}" class="flex-1 md:flex-initial px-4 py-2.5 bg-[#ed1e28] hover:bg-[#d41720] text-white text-xs font-bold rounded-xl shadow-sm text-center transition">
                        Pesan Menu Lagi
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari akun?');" class="flex-1 md:flex-initial">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl text-center transition">
                        Keluar Akun
                    </button>
                </form>
            </div>
        </div>

        <!-- Laporan Pengeluaran Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-2xs">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">TOTAL PENGELUARAN</div>
                <div class="text-base sm:text-xl lg:text-2xl font-black text-[#ed1e28] mt-1">
                    Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-emerald-600 mt-1 font-bold">✓ Seluruh transaksi lunas</div>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-2xs">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">PENGELUARAN BULAN INI</div>
                <div class="text-base sm:text-xl lg:text-2xl font-black text-[#1a1c1d] mt-1">
                    Rp {{ number_format($monthExpenses, 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-gray-400 mt-1">{{ Carbon\Carbon::now()->translatedFormat('F Y') }}</div>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-2xs">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">PENGELUARAN HARI INI</div>
                <div class="text-base sm:text-xl lg:text-2xl font-black text-amber-600 mt-1">
                    Rp {{ number_format($todayExpenses, 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-amber-700 mt-1 font-semibold">{{ Carbon\Carbon::today()->translatedFormat('d F Y') }}</div>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-2xs">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">TOTAL PESANAN</div>
                <div class="text-base sm:text-xl lg:text-2xl font-black text-[#1a1c1d] mt-1">
                    {{ $totalOrdersCount }} Kali
                </div>
                <div class="text-[10px] text-gray-400 mt-1">Rata-rata: Rp {{ number_format($avgExpense, 0, ',', '.') }}/order</div>
            </div>
        </div>

        <!-- Tabbed Navigation Content (Riwayat Pesanan, Laporan Pengeluaran, Edit Biodata) -->
        <div class="space-y-4">
            
            <!-- Tab Headers -->
            <div class="flex items-center justify-between border-b border-gray-200 overflow-x-auto no-scrollbar pb-1 gap-2">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="switchProfileTab('riwayat')" id="tab-btn-riwayat" class="px-4 py-2.5 text-xs font-bold rounded-2xl transition border-b-2 border-[#ed1e28] text-[#ed1e28] bg-red-50/50">
                        📋 Riwayat Pesanan ({{ $orders->count() }})
                    </button>
                    <button type="button" onclick="switchProfileTab('pengeluaran')" id="tab-btn-pengeluaran" class="px-4 py-2.5 text-xs font-bold rounded-2xl transition border-b-2 border-transparent text-[#5b5c61] hover:bg-gray-100">
                        📊 Rincian Stan & Pengeluaran
                    </button>
                </div>
            </div>

            <!-- Tab 1: Riwayat Pesanan -->
            <div id="tab-content-riwayat" class="space-y-4">
                @if($orders->isEmpty())
                    <div class="bg-white rounded-3xl p-10 text-center border border-gray-100 shadow-xs max-w-md mx-auto my-6">
                        <div class="w-16 h-16 rounded-full bg-red-50 text-2xl flex items-center justify-center mx-auto mb-3">
                            📋
                        </div>
                        <h3 class="text-sm font-bold text-[#1a1c1d]">Belum Ada Riwayat Pesanan</h3>
                        <p class="text-xs text-[#5b5c61] mt-1 mb-4">
                            Anda belum pernah memesan makanan. Nikmati kemudahan memesan makanan kantin tanpa antre.
                        </p>
                        @if($user->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-[#ed1e28] text-white text-xs font-bold rounded-xl shadow-sm inline-block">
                                Buka Dashboard Admin
                            </a>
                        @elseif($user->isPenjual())
                            <a href="{{ route('merchant.dashboard') }}" class="px-4 py-2 bg-[#ed1e28] text-white text-xs font-bold rounded-xl shadow-sm inline-block">
                                Buka Pesanan Masuk Stan
                            </a>
                        @else
                            <a href="{{ route('home') }}" class="px-4 py-2 bg-[#ed1e28] text-white text-xs font-bold rounded-xl shadow-sm inline-block">
                                Lihat Menu Kantin
                            </a>
                        @endif
                    </div>
                @else
                    <div class="space-y-3" id="profile-orders-list">
                        @foreach($orders as $order)
                            <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-100 shadow-2xs hover:shadow-sm transition flex flex-col sm:flex-row justify-between gap-4">
                                <div class="space-y-1.5 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-xs font-black text-[#ed1e28]">{{ $order->stall->name ?? 'Kantin Tel-U' }}</span>
                                        <span class="text-gray-300">•</span>
                                        <span class="text-[11px] text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $order->status === 'siap_diambil' ? 'bg-emerald-100 text-emerald-800' : ($order->status === 'selesai' ? 'bg-gray-100 text-gray-700' : 'bg-amber-100 text-amber-800') }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </div>

                                    <div class="text-xs sm:text-sm font-black text-[#1a1c1d]">
                                        {{ $order->items->pluck('menu_name')->implode(', ') }}
                                    </div>

                                    <div class="text-xs text-[#5b5c61] flex items-center gap-3">
                                        <span>Kode: <strong class="text-gray-700">{{ $order->order_code }}</strong></span>
                                        @if($order->queue_number)
                                            <span>Antrean: <strong class="text-[#ed1e28]">{{ $order->queue_number }}</strong></span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex sm:flex-col items-center sm:items-end justify-between gap-2.5 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                                    <div class="text-right">
                                        <div class="text-[10px] text-gray-400">Total</div>
                                        <div class="text-sm font-black text-[#ed1e28]">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('orders.receipt', $order->id) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">
                                            Struk
                                        </a>
                                        <a href="{{ route('orders.show', $order->id) }}" class="px-3.5 py-1.5 bg-[#ed1e28] hover:bg-[#d41720] text-white rounded-xl text-xs font-bold shadow-sm transition">
                                            Pantau
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Tab 2: Laporan Pengeluaran & Rincian Stan -->
            <div id="tab-content-pengeluaran" class="hidden space-y-4">
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-2xs space-y-4">
                    <div>
                        <h3 class="text-sm font-black text-[#1a1c1d]">Rincian Pengeluaran Berdasarkan Stan Kantin</h3>
                        <p class="text-xs text-[#5b5c61]">Analisis stan kuliner favorit dan total dana yang telah Anda belanjakan</p>
                    </div>

                    @if($spendingByStall->isEmpty())
                        <div class="py-8 text-center text-xs text-gray-400">
                            Belum ada riwayat transaksi lunas.
                        </div>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach($spendingByStall as $stallSummary)
                                <div class="py-3 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-red-50 text-[#ed1e28] font-bold flex items-center justify-center text-sm shrink-0">
                                            🏪
                                        </div>
                                        <div>
                                            <div class="text-xs font-black text-[#1a1c1d]">{{ $stallSummary['stall_name'] }}</div>
                                            <div class="text-[11px] text-gray-400">Stan {{ $stallSummary['stall_code'] }} • {{ $stallSummary['order_count'] }} kali pesan</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs sm:text-sm font-black text-[#ed1e28]">
                                            Rp {{ number_format($stallSummary['total_amount'], 0, ',', '.') }}
                                        </div>
                                        <div class="text-[10px] text-gray-400">
                                            {{ $totalExpenses > 0 ? round(($stallSummary['total_amount'] / $totalExpenses) * 100) : 0 }}% dari total belanja
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            </div>

        </div>

    </main>

    <!-- Modal Edit Biodata Akun (All Roles: Pembeli, Penjual, Admin) -->
    <div id="biodataModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-xl w-full p-5 sm:p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto space-y-4 relative">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-red-50 text-[#ed1e28] flex items-center justify-center text-sm font-black">
                        ⚙️
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-[#1a1c1d]">Edit Biodata Akun</h3>
                        <p class="text-[11px] text-[#5b5c61]">Perbarui foto profil, nama lengkap, kontak, atau ganti kata sandi.</p>
                    </div>
                </div>
                <button type="button" onclick="closeBiodataModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition active:scale-95" title="Tutup">
                    ✕
                </button>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Foto Profil Akun Upload -->
                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1.5">Foto Profil Akun</label>
                    <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-2xl border border-gray-200/80">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl overflow-hidden bg-[#ed1e28] text-white font-black text-xl flex items-center justify-center shrink-0 border border-gray-200">
                            @if($user->avatar)
                                <img id="avatarPreview" src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <span id="avatarPreviewInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="space-y-1">
                            <label class="cursor-pointer px-3.5 py-1.5 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-xl transition inline-block active:scale-95 shadow-2xs">
                                📷 Pilih Foto Baru
                                <input type="file" id="profileAvatarInput" name="avatar" accept="image/*" class="hidden" onchange="previewUserAvatar(this)">
                            </label>
                            <p class="text-[10px] text-gray-400">Format: JPG, PNG, atau WebP (Maks. 5MB).</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] font-semibold focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Username Civitas</label>
                        <div class="flex items-center bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 focus-within:border-[#ed1e28] focus-within:bg-white transition">
                            <span class="text-xs text-gray-400 font-bold mr-1">@</span>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full bg-transparent text-xs text-[#1a1c1d] font-bold focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Email (Terdaftar)</label>
                        <input type="email" value="{{ $user->email }}" disabled
                            class="w-full bg-gray-100 border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-gray-500 cursor-not-allowed">
                        <span class="text-[10px] text-gray-400">Email civitas tidak dapat diubah</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Nomor WhatsApp / HP <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                            class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"
                            placeholder="08123456789">
                    </div>
                </div>

                <!-- Change Password Section -->
                <div class="pt-3 border-t border-gray-100 space-y-2.5">
                    <div class="text-xs font-bold text-[#1a1c1d]">Ganti Kata Sandi (Kosongkan bila tidak ingin mengubah)</div>

                    <div>
                        <label class="block text-xs text-[#5b5c61] mb-1">Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password"
                            class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"
                            placeholder="Masukkan kata sandi lama">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-[#5b5c61] mb-1">Kata Sandi Baru</label>
                            <input type="password" name="new_password"
                                class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"
                                placeholder="Minimal 6 karakter">
                        </div>
                        <div>
                            <label class="block text-xs text-[#5b5c61] mb-1">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="new_password_confirmation"
                                class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition"
                                placeholder="Ulangi kata sandi baru">
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeBiodataModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#ed1e28] hover:bg-[#d41720] text-white text-xs font-bold rounded-xl shadow-sm transition active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Role-Aware Mobile Bottom Navigation (md:hidden) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 px-4 py-2 shadow-lg flex items-center justify-around">
        @if($user->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span class="text-[10px] font-bold">Kelola Akun</span>
            </a>
            <a href="{{ route('admin.transactions') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="text-[10px] font-bold">Transaksi</span>
            </a>
            <a href="{{ route('admin.stalls.categories') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span class="text-[10px] font-bold">Stan & Kat</span>
            </a>
            <a href="{{ route('admin.reports') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="text-[10px] font-bold">Laporan</span>
            </a>
            <a href="{{ route('profile.index') }}" class="flex flex-col items-center gap-1 text-[#ed1e28]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] font-black">Biodata</span>
            </a>
        @elseif($user->isPenjual())
            <a href="{{ route('merchant.dashboard') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="text-[10px] font-bold">Pesanan</span>
            </a>
            <a href="{{ route('merchant.menus.index') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span class="text-[10px] font-bold">Menu & Stok</span>
            </a>
            <a href="{{ route('merchant.reports') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="text-[10px] font-bold">Pendapatan</span>
            </a>
            <a href="{{ route('merchant.stall.profile') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span class="text-[10px] font-bold">Profil Stan</span>
            </a>
            <a href="{{ route('profile.index') }}" class="flex flex-col items-center gap-1 text-[#ed1e28]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] font-black">Biodata</span>
            </a>
        @else
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[10px] font-bold">Menu</span>
            </a>
            <a href="{{ route('cart.index') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span class="text-[10px] font-bold">Keranjang</span>
            </a>
            <a href="{{ route('orders.index') }}" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="text-[10px] font-bold">Pesanan</span>
            </a>
            <a href="{{ route('profile.index') }}" class="flex flex-col items-center gap-1 text-[#ed1e28]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] font-black">Biodata</span>
            </a>
        @endif
    </nav>

</div>

@push('scripts')
<script>
    function openBiodataModal() {
        const modal = document.getElementById('biodataModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeBiodataModal() {
        const modal = document.getElementById('biodataModal');
        if (modal) modal.classList.add('hidden');
    }

    function triggerAvatarUpload() {
        openBiodataModal();
        const input = document.getElementById('profileAvatarInput');
        if (input) {
            input.click();
        }
    }

    function previewUserAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Update preview in form
                const av = document.getElementById('avatarPreview');
                if (av) av.src = e.target.result;

                // Update main header avatar display
                const mainDisplay = document.getElementById('mainAvatarDisplay');
                if (mainDisplay) {
                    mainDisplay.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function switchProfileTab(tabName) {
        if (tabName === 'biodata') {
            openBiodataModal();
            return;
        }
        // Tab buttons
        const tabs = ['riwayat', 'pengeluaran'];
        tabs.forEach(t => {
            const btn = document.getElementById('tab-btn-' + t);
            const content = document.getElementById('tab-content-' + t);
            if (t === tabName) {
                if (btn) btn.className = 'px-4 py-2.5 text-xs font-bold rounded-2xl transition border-b-2 border-[#ed1e28] text-[#ed1e28] bg-red-50/50';
                if (content) content.classList.remove('hidden');
            } else {
                if (btn) btn.className = 'px-4 py-2.5 text-xs font-bold rounded-2xl transition border-b-2 border-transparent text-[#5b5c61] hover:bg-gray-100';
                if (content) content.classList.add('hidden');
            }
        });
    }

    // Auto-open requested tab or modal if specified in URL
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'biodata' || window.location.hash === '#biodata') {
            openBiodataModal();
        }

        // Close modal on Escape or backdrop click
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeBiodataModal();
        });
        const modal = document.getElementById('biodataModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeBiodataModal();
            });
        }
    });

    // Auto-refresh orders every 2 seconds if there are active orders
    const hasActiveOrders = {{ $orders->whereIn('status', ['menunggu', 'diproses', 'siap_diambil'])->isNotEmpty() ? 'true' : 'false' }};
    if (hasActiveOrders) {
        setInterval(async () => {
            if (document.activeElement && document.activeElement.matches('input, textarea, select')) return;
            try {
                const res = await fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newOrdersList = doc.getElementById('profile-orders-list');
                const curOrdersList = document.getElementById('profile-orders-list');
                if (newOrdersList && curOrdersList && curOrdersList.innerHTML !== newOrdersList.innerHTML) {
                    curOrdersList.innerHTML = newOrdersList.innerHTML;
                }
            } catch(e) {}
        }, 2000);
    }
</script>
@endpush
@endsection