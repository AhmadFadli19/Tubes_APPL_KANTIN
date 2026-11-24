@extends('layouts.app', ['title' => 'Admin — Kelola Akun Civitas & Stan Kantin'])

@section('content')
<div class="w-full min-h-screen bg-[#faf9fb] flex flex-col justify-between pb-24 md:pb-12">

    <!-- Top Navigation Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-gray-200/80 px-4 sm:px-8 py-3.5 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
            <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 sm:gap-3 hover:opacity-85 transition shrink-0" title="Kantin Tel-U">
                <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Tel-U" class="w-8 h-8 sm:w-10 sm:h-10 object-contain shrink-0 drop-shadow-2xs" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <span class="text-xs sm:text-sm font-black text-[#1a1c1d] tracking-tight uppercase hover:text-[#ed1e28] transition truncate">KANTIN TEL-U</span>
                        <span class="bg-red-50 text-[#ed1e28] text-[9px] sm:text-[10px] font-bold px-1.5 sm:px-2 py-0.5 rounded-md shrink-0">SUPER ADMIN</span>
                    </div>
                    <div class="text-[10px] sm:text-[11px] text-[#5b5c61] truncate hidden xs:block">Direktorat Logistik & Fasilitas Kampus</div>
                </div>
            </a>

            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <nav class="hidden md:flex items-center gap-1.5 lg:gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#ed1e28] text-white shadow-2xs' : 'text-[#5b5c61] hover:bg-gray-100' }}">Kelola Akun</a>
                    <a href="{{ route('admin.transactions') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-[#5b5c61] hover:bg-gray-100 transition">Pantau Transaksi</a>
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

                <!-- Logout Button -->
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

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-6 space-y-6 flex-1">

        <!-- Title & Action Buttons Row -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-[#ed1e28] uppercase tracking-wider">
                    <span>MANAJEMEN AKUN & HAK AKSES KANTIN TEL-U</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-[#1a1c1d] tracking-tight mt-1">Pusat Kelola Pengguna & Stan Penjual</h1>
                <p class="text-xs text-[#5b5c61] mt-1 max-w-2xl leading-relaxed">
                    Admin dapat membuat akun penjual dengan biodata stan lengkap, memverifikasi permohonan pendaftaran penjual, mereset kata sandi semua pengguna, dan mengatur hak akses akun.
                </p>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" onclick="openCreateSellerModal()" class="px-4 py-2.5 bg-[#ed1e28] hover:bg-[#bb0016] text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2 active:scale-95">
                    <span>+</span>
                    <span>Buat Akun Penjual Baru</span>
                </button>
                <a href="{{ route('admin.reports', ['export' => 'csv']) }}" class="px-4 py-2.5 bg-white border border-gray-200 text-[#1a1c1d] font-bold text-xs rounded-xl shadow-2xs hover:bg-gray-50 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Export CSV</span>
                </a>
            </div>
        </div>

        <!-- 4 Metrics Row (Compact 2 cols on mobile, 4 on desktop) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
            <div class="bg-white rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-gray-100 shadow-2xs flex items-center justify-between">
                <div class="min-w-0">
                    <div class="text-[10px] sm:text-xs text-[#5b5c61] truncate font-medium">Total Akun Terdaftar</div>
                    <div class="text-lg sm:text-2xl font-black text-[#1a1c1d] mt-0.5 sm:mt-1">{{ number_format($totalUsers, 0, ',', '.') }}</div>
                    <div class="text-[9px] sm:text-[10px] text-emerald-600 font-bold truncate mt-0.5">Pembeli, Penjual & Admin</div>
                </div>
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-rose-50 text-[#ed1e28] flex items-center justify-center font-bold text-base sm:text-xl shrink-0">👥</div>
            </div>

            <div class="bg-white rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-gray-100 shadow-2xs flex items-center justify-between">
                <div class="min-w-0">
                    <div class="text-[10px] sm:text-xs text-[#5b5c61] truncate font-medium">Stan Kantin Terdaftar</div>
                    <div class="text-lg sm:text-2xl font-black text-[#1a1c1d] mt-0.5 sm:mt-1">{{ $totalStalls }} Stan</div>
                    <div class="text-[9px] sm:text-[10px] text-[#5b5c61] truncate mt-0.5">Gedung GKU & Selaru</div>
                </div>
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-base sm:text-xl shrink-0">🏪</div>
            </div>

            <div class="bg-white rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-gray-100 shadow-2xs flex items-center justify-between">
                <div class="min-w-0">
                    <div class="text-[10px] sm:text-xs text-[#5b5c61] truncate font-medium">Mahasiswa / Pembeli</div>
                    <div class="text-lg sm:text-2xl font-black text-[#1a1c1d] mt-0.5 sm:mt-1">{{ number_format($activeBuyers, 0, ',', '.') }}</div>
                    <div class="text-[9px] sm:text-[10px] text-emerald-600 font-bold truncate mt-0.5">Civitas Akademika Tel-U</div>
                </div>
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-base sm:text-xl shrink-0">🎓</div>
            </div>

            <div class="bg-white rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-gray-100 shadow-2xs flex items-center justify-between">
                <div class="min-w-0">
                    <div class="text-[10px] sm:text-xs text-[#5b5c61] truncate font-medium">Permintaan Buka Stan</div>
                    <div class="text-lg sm:text-2xl font-black {{ $pendingRequestsCount > 0 ? 'text-[#ed1e28]' : 'text-gray-800' }} mt-0.5 sm:mt-1">
                        {{ $pendingRequestsCount }} Menunggu
                    </div>
                    <div class="text-[9px] sm:text-[10px] {{ $pendingRequestsCount > 0 ? 'text-red-600 font-bold' : 'text-gray-400' }} truncate mt-0.5">
                        {{ $pendingRequestsCount > 0 ? 'Perlu tindakan admin' : 'Semua telah diproses' }}
                    </div>
                </div>
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl {{ $pendingRequestsCount > 0 ? 'bg-red-100 text-[#ed1e28]' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center font-bold text-base sm:text-xl shrink-0">📬</div>
            </div>
        </div>

        <!-- Section: Permintaan Calon Penjual (Seller Requests) -->
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-2xs overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-black text-[#1a1c1d]">Permintaan Pendaftaran Akun Penjual Baru</h2>
                        @if($pendingRequestsCount > 0)
                            <span class="bg-[#ed1e28] text-white text-[10px] font-black px-2.5 py-0.5 rounded-full animate-pulse">
                                {{ $pendingRequestsCount }} Perlu Tindakan
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-[#5b5c61] mt-0.5">Pengajuan kemitraan stan dari calon penjual atau civitas yang ingin berjualan di kantin</p>
                </div>
            </div>

            @if($sellerRequests->isEmpty())
                <div class="p-8 text-center text-xs text-gray-400">
                    Belum ada permintaan pendaftaran akun penjual saat ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50/90 text-gray-500 uppercase text-[10px] font-bold tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-2.5">Nama Pemohon & Kontak</th>
                                <th class="px-4 py-2.5">Nama Stan & Lokasi</th>
                                <th class="px-4 py-2.5">Jam Buka & Menu Utama</th>
                                <th class="px-4 py-2.5">Rekening / Bank</th>
                                <th class="px-4 py-2.5">Status</th>
                                <th class="px-4 py-2.5 text-right">Tindakan Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($sellerRequests as $req)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-4 py-2.5">
                                        <div class="font-bold text-gray-900">{{ $req->applicant_name }}</div>
                                        <div class="text-[11px] text-gray-500">{{ $req->email }} • {{ $req->phone }}</div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="font-black text-[#ed1e28]">{{ $req->stall_name }}</div>
                                        <div class="text-[11px] text-gray-600">{{ $req->stall_location }}</div>
                                    </td>
                                    <td class="px-4 py-2.5 max-w-xs">
                                        <div class="font-semibold text-gray-800">⏱️ {{ $req->opening_hours }}</div>
                                        <div class="text-[11px] text-gray-500 line-clamp-2 mt-0.5">{{ $req->stall_description }}</div>
                                    </td>
                                    <td class="px-4 py-2.5 text-gray-700">
                                        {{ $req->bank_info ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $req->status === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : ($req->status === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                            {{ $req->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right">
                                        @if($req->status === 'menunggu')
                                            <div class="flex items-center justify-end gap-2">
                                                <!-- Setujui Form -->
                                                <form method="POST" action="{{ route('admin.seller-requests.approve', $req->id) }}" onsubmit="return confirm('Setujui pendaftaran ini? Akun penjual dan stan akan otomatis dibuat.')">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
                                                        Setujui Buka Stan ✓
                                                    </button>
                                                </form>

                                                <!-- Tolak Form -->
                                                <form method="POST" action="{{ route('admin.seller-requests.reject', $req->id) }}" onsubmit="return confirm('Tolak pendaftaran ini?')">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-red-50 text-gray-600 hover:text-red-600 rounded-xl text-xs font-bold transition">
                                                        Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-[11px] text-gray-400 font-medium">Telah diproses</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Section: Kelola Akun Pengguna (User Management Table) -->
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-2xs overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-sm font-black text-[#1a1c1d]">Daftar Seluruh Pengguna & Hak Akses</h2>
                    <p class="text-xs text-[#5b5c61]">Kelola akun mahasiswa, penjual kantin, dan super admin</p>
                </div>

                <!-- Filter & Search Controls -->
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-2xl text-xs font-bold">
                        <a href="{{ route('admin.dashboard', array_merge(request()->except('role', 'page'), ['role' => 'all'])) }}" class="px-3 py-1.5 rounded-xl transition {{ $roleFilter === 'all' ? 'bg-white text-[#ed1e28] shadow-xs' : 'text-gray-600 hover:text-black' }}">Semua</a>
                        <a href="{{ route('admin.dashboard', array_merge(request()->except('role', 'page'), ['role' => 'pembeli'])) }}" class="px-3 py-1.5 rounded-xl transition {{ $roleFilter === 'pembeli' ? 'bg-white text-[#ed1e28] shadow-xs' : 'text-gray-600 hover:text-black' }}">Pembeli</a>
                        <a href="{{ route('admin.dashboard', array_merge(request()->except('role', 'page'), ['role' => 'penjual'])) }}" class="px-3 py-1.5 rounded-xl transition {{ $roleFilter === 'penjual' ? 'bg-white text-[#ed1e28] shadow-xs' : 'text-gray-600 hover:text-black' }}">Penjual</a>
                        <a href="{{ route('admin.dashboard', array_merge(request()->except('role', 'page'), ['role' => 'admin'])) }}" class="px-3 py-1.5 rounded-xl transition {{ $roleFilter === 'admin' ? 'bg-white text-[#ed1e28] shadow-xs' : 'text-gray-600 hover:text-black' }}">Admin</a>
                    </div>

                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, hp..." class="bg-[#faf9fb] border border-gray-200 text-xs rounded-2xl px-3.5 py-1.5 pl-8 focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                        <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </form>
            </div>

            <!-- Table of Users -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50/90 text-gray-500 uppercase text-[10px] font-bold tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-2.5">Pengguna</th>
                            <th class="px-4 py-2.5">Peran (Role)</th>
                            <th class="px-4 py-2.5">Stan yang Dikelola</th>
                            <th class="px-4 py-2.5">Status Akun</th>
                            <th class="px-4 py-2.5 text-right">Aksi & Reset Sandi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full overflow-hidden bg-[#1a1c1d] text-white font-bold flex items-center justify-center shrink-0 border border-gray-200">
                                            @if($user->avatar)
                                                <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                                            @else
                                                <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-gray-900 truncate">{{ $user->name }}</div>
                                            <div class="text-[10px] text-gray-500 truncate">{{ $user->email }} • {{ $user->phone ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : ($user->role === 'penjual' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    @if($user->stall)
                                        <span class="font-bold text-[#ed1e28] block">{{ $user->stall->name }}</span>
                                        <span class="text-[9px] text-gray-400 block">{{ $user->stall->code ?? '' }} • {{ $user->stall->location }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5">
                                    <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
                                        @csrf
                                        <button type="submit" class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase border transition {{ $user->status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200' }}">
                                            ● {{ $user->status }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Ubah Kata Sandi Modal Trigger -->
                                        <button type="button" onclick="openResetPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}')" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-[10px] font-bold transition flex items-center gap-1 shadow-2xs">
                                            <span>🔑</span>
                                            <span>Ubah Sandi</span>
                                        </button>

                                        <!-- Delete User -->
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" onsubmit="return confirm('Yakin ingin menghapus akun {{ addslashes($user->name) }}?')">
                                                @csrf
                                                <button type="submit" class="p-1 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" title="Hapus Akun">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        </div>

    </main>

    <!-- Modal Daftarkan Penjual & Stan Baru (Space-Efficient Admin Modal) -->
    <div id="createSellerModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl overflow-hidden shadow-2xl border border-gray-100 p-6 space-y-4 max-h-[90vh] overflow-y-auto relative">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-red-50 text-[#ed1e28] flex items-center justify-center text-sm font-black">
                        🏪
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-[#1a1c1d]">Daftarkan Akun Penjual Baru & Buat Stan</h3>
                        <p class="text-[11px] text-[#5b5c61]">Buat akun pemilik stan dan konfigurasikan lokasi serta jam operasional.</p>
                    </div>
                </div>
                <button type="button" onclick="closeCreateSellerModal()" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 flex items-center justify-center transition active:scale-95" title="Tutup">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.sellers.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Pemilik Section -->
                <div class="space-y-3">
                    <span class="text-[10px] font-bold text-[#ed1e28] uppercase tracking-wider block">1. Data Akun Penjual (Pemilik)</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Nama Pemilik Stan <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition" placeholder="Contoh: Bu Siti Aminah">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Email Login <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition" placeholder="siti@gmail.com">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">WhatsApp <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" required class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition" placeholder="08123456789">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Kata Sandi Awal <span class="text-red-500">*</span></label>
                            <input type="text" name="password" value="password123" required class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                        </div>
                    </div>
                </div>

                <!-- Biodata Stan Section -->
                <div class="space-y-3 pt-3 border-t border-gray-100">
                    <span class="text-[10px] font-bold text-[#ed1e28] uppercase tracking-wider block">2. Biodata Stan & Informasi Usaha</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Nama Stan Kuliner <span class="text-red-500">*</span></label>
                            <input type="text" name="new_stall_name" required class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition" placeholder="Contoh: Ayam Geprek Bu Siti">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Kode Stan (Blok)</label>
                            <input type="text" name="new_stall_code" placeholder="A-01" class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Lokasi di Kantin <span class="text-red-500">*</span></label>
                            <input type="text" name="new_stall_location" value="Lantai 1 Blok A Kantin GKU Tel-U" required class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Jam Operasional</label>
                            <input type="text" name="new_stall_opening_hours" value="08:00 - 17:00 WIB" class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Info Rekening / Bank</label>
                            <input type="text" name="new_stall_bank_info" placeholder="BCA 82718291 a.n Pemilik" class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Foto / Banner Stan</label>
                            <input type="file" name="new_stall_image" accept="image/*" class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3 py-1.5 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Deskripsi Kuliner Stan</label>
                            <textarea name="new_stall_description" rows="2" class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition" placeholder="Keunggulan masakan dan hidangan utama yang dijual..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeCreateSellerModal()" class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-[#ed1e28] hover:bg-[#d41720] text-white rounded-xl text-xs font-bold shadow-md shadow-red-500/20 active:scale-95 transition flex items-center gap-1.5">
                        <span>Daftarkan Akun Penjual</span>
                        <span>→</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ubah Kata Sandi (SRS Admin dapat mengubah-ubah kata sandi semua pengguna) -->
    <div id="resetPasswordModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-3xl overflow-hidden shadow-2xl border border-gray-100 p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-black text-[#1a1c1d]">Ubah Kata Sandi Pengguna</h3>
                    <p class="text-[11px] text-gray-500" id="resetModalSubtitle">Reset kata sandi pengguna</p>
                </div>
                <button type="button" onclick="closeResetPasswordModal()" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 flex items-center justify-center">✕</button>
            </div>

            <form id="resetPasswordForm" method="POST" action="" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#1a1c1d] mb-1">Kata Sandi Baru <span class="text-red-500">*</span></label>
                    <input type="text" name="password" value="password123" required minlength="6" class="w-full bg-[#faf9fb] border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-[#1a1c1d] focus:bg-white focus:outline-none focus:border-[#ed1e28] transition">
                    <p class="text-[10px] text-gray-400 mt-1">Minimal 6 karakter. Langsung diperbarui tanpa butuh kata sandi lama.</p>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeResetPasswordModal()" class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-[#ed1e28] hover:bg-[#d41720] text-white rounded-xl text-xs font-black shadow-md shadow-red-500/20 active:scale-95 transition">Perbarui Kata Sandi</button>
                </div>
            </form>
        </div>
    </div>

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
    function openCreateSellerModal() {
        const modal = document.getElementById('createSellerModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeCreateSellerModal() {
        const modal = document.getElementById('createSellerModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function openResetPasswordModal(userId, userName, userEmail) {
        document.getElementById('resetModalSubtitle').textContent = userName + ' (' + userEmail + ')';
        document.getElementById('resetPasswordForm').action = '{{ url("/admin/users") }}/' + userId + '/reset-password';
        const modal = document.getElementById('resetPasswordModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeResetPasswordModal() {
        const modal = document.getElementById('resetPasswordModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateSellerModal();
            closeResetPasswordModal();
        }
    });

    const createSellerModal = document.getElementById('createSellerModal');
    if (createSellerModal) {
        createSellerModal.addEventListener('click', function(e) {
            if (e.target === this) closeCreateSellerModal();
        });
    }

    const resetModal = document.getElementById('resetPasswordModal');
    if (resetModal) {
        resetModal.addEventListener('click', function(e) {
            if (e.target === this) closeResetPasswordModal();
        });
    }
</script>
@endpush
@endsection
