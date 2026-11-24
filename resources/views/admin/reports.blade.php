@extends('layouts.app', ['title' => 'Admin — Laporan Penjualan Seluruh Stan'])

@section('content')
<div class="w-full min-h-screen bg-[#faf9fb] flex flex-col justify-between pb-24 md:pb-12">

    <!-- Top Navigation Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-gray-200/80 px-4 sm:px-8 py-3.5 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
            <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 sm:gap-3 hover:opacity-85 transition shrink-0" title="Kantin Tel-U">
                <img src="{{ asset('assets/figma/8f2c8.png') }}" alt="Tel-U" class="w-8 h-8 sm:w-10 sm:h-10 object-contain shrink-0 drop-shadow-2xs" onerror="this.onerror=null; this.src='{{ asset('assets/figma/6a55e.png') }}';">
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <span class="text-xs sm:text-sm font-black text-[#1a1c1d] tracking-tight uppercase hover:text-[#ed1e28] transition truncate">LAPORAN KONSOLIDASI</span>
                        <span class="bg-red-50 text-[#ed1e28] text-[9px] sm:text-[10px] font-bold px-1.5 sm:px-2 py-0.5 rounded-md shrink-0">KEUANGAN</span>
                    </div>
                    <div class="text-[10px] sm:text-[11px] text-[#5b5c61] truncate hidden xs:block">Rekapitulasi Omzet & Transaksi Seluruh Stan Kantin</div>
                </div>
            </a>

            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <nav class="hidden md:flex items-center gap-1.5 lg:gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-[#5b5c61] hover:bg-gray-100 transition">Kelola Akun</a>
                    <a href="{{ route('admin.transactions') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-[#5b5c61] hover:bg-gray-100 transition">Pantau Transaksi</a>
                    <a href="{{ route('admin.stalls.categories') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-[#5b5c61] hover:bg-gray-100 transition">Stan & Kategori</a>
                    <a href="{{ route('admin.reports') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#ed1e28] text-white shadow-2xs">Laporan Penjualan</a>
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

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-6 space-y-6 flex-1">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-black text-[#1a1c1d] tracking-tight">Laporan Keuangan & Penjualan Stan</h1>
                <p class="text-xs text-[#5b5c61] mt-0.5">Analisis pendapatan konsolidasi mitra kantin Telkom University</p>
            </div>

            <div class="flex items-center gap-2">
                <!-- Period Selector -->
                <div class="flex items-center bg-gray-100 p-1 rounded-xl text-xs font-bold">
                    <a href="{{ route('admin.reports', ['period' => 'day']) }}" class="px-3 py-1.5 rounded-lg transition {{ $period === 'day' ? 'bg-white text-[#ed1e28] shadow-xs' : 'text-[#5b5c61]' }}">
                        Hari Ini
                    </a>
                    <a href="{{ route('admin.reports', ['period' => 'week']) }}" class="px-3 py-1.5 rounded-lg transition {{ $period === 'week' ? 'bg-white text-[#ed1e28] shadow-xs' : 'text-[#5b5c61]' }}">
                        Minggu Ini
                    </a>
                    <a href="{{ route('admin.reports', ['period' => 'month']) }}" class="px-3 py-1.5 rounded-lg transition {{ $period === 'month' ? 'bg-white text-[#ed1e28] shadow-xs' : 'text-[#5b5c61]' }}">
                        Bulan Ini
                    </a>
                </div>

                <!-- Action Buttons (Print & Excel) -->
                <a href="{{ route('admin.reports', ['period' => $period, 'export' => 'excel']) }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Unduh Excel</span>
                </a>

                <button onclick="window.print()" class="px-3.5 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 print:hidden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak</span>
                </button>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/80 shadow-2xs">
                <div class="text-[11px] text-[#5b5c61] font-bold uppercase tracking-wider">Total Pendapatan Konsolidasi</div>
                <div class="text-xl sm:text-2xl font-black text-[#ed1e28] mt-1">Rp {{ number_format($totalRevenueAll, 0, ',', '.') }}</div>
                <div class="text-[10px] text-emerald-600 font-bold mt-1">100% Non-Tunai QRIS Terverifikasi</div>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/80 shadow-2xs">
                <div class="text-[11px] text-[#5b5c61] font-bold uppercase tracking-wider">Total Pesanan Selesai</div>
                <div class="text-xl sm:text-2xl font-black text-[#1a1c1d] mt-1">{{ $totalOrdersAll }} Transaksi</div>
                <div class="text-[10px] text-[#5b5c61] mt-1">Dari {{ $stallsSummary->count() }} Stan Terdaftar</div>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/80 shadow-2xs">
                <div class="text-[11px] text-[#5b5c61] font-bold uppercase tracking-wider">Rata-rata Pendapatan / Stan</div>
                @php
                    $avgPerStall = $stallsSummary->count() > 0 ? $totalRevenueAll / $stallsSummary->count() : 0;
                @endphp
                <div class="text-xl sm:text-2xl font-black text-[#1a1c1d] mt-1">Rp {{ number_format($avgPerStall, 0, ',', '.') }}</div>
                <div class="text-[10px] text-emerald-600 font-bold mt-1">Pertumbuhan Positif</div>
            </div>
        </div>

        <!-- Stalls Revenue Table (Req. SMPEK-640) -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-[#1a1c1d] uppercase tracking-wider">Rincian Penjualan per Stan Kantin</h3>
                <span class="text-[10px] text-[#5b5c61]">Periode: {{ strtoupper($period) }}</span>
            </div>

            <!-- Desktop View Table -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-[#5b5c61] uppercase tracking-wider text-[10px]">
                            <th class="py-2.5 px-3.5 font-bold">No</th>
                            <th class="py-2.5 px-3.5 font-bold">Nama Stan Kantin</th>
                            <th class="py-2.5 px-3.5 font-bold">Lokasi</th>
                            <th class="py-2.5 px-3.5 font-bold text-center">Jumlah Pesanan</th>
                            <th class="py-2.5 px-3.5 font-bold text-right">Total Pendapatan (IDR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stallsSummary as $index => $row)
                            <tr class="hover:bg-rose-50/20 transition">
                                <td class="py-2.5 px-3.5 font-bold text-[#5b5c61]">{{ $index + 1 }}</td>
                                <td class="py-2.5 px-3.5 font-bold text-[#1a1c1d]">{{ $row->name }}</td>
                                <td class="py-2.5 px-3.5 text-[#5b5c61]">{{ $row->location }}</td>
                                <td class="py-2.5 px-3.5 text-center font-bold text-[#1a1c1d]">{{ $row->orders_count }}</td>
                                <td class="py-2.5 px-3.5 text-right font-extrabold text-[#ed1e28]">
                                    Rp {{ number_format($row->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-[#5b5c61]">
                                    Tidak ada data penjualan pada periode yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200 font-bold text-xs">
                        <tr>
                            <td colspan="3" class="py-2.5 px-3.5 text-right text-[#1a1c1d] uppercase">TOTAL KESELURUHAN:</td>
                            <td class="py-2.5 px-3.5 text-center text-[#1a1c1d]">{{ $totalOrdersAll }} Pesanan</td>
                            <td class="py-2.5 px-3.5 text-right text-base text-[#ed1e28] font-black">
                                Rp {{ number_format($totalRevenueAll, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Mobile View Compact Cards -->
            <div class="sm:hidden divide-y divide-gray-100">
                @forelse($stallsSummary as $index => $row)
                    <div class="p-4 space-y-1.5 hover:bg-gray-50/50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-red-100 text-[#ed1e28] text-[10px] font-black flex items-center justify-center">{{ $index + 1 }}</span>
                                <span class="font-bold text-[#1a1c1d] text-xs">{{ $row->name }}</span>
                            </div>
                            <span class="font-black text-[#ed1e28] text-xs">Rp {{ number_format($row->total_revenue, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-[#5b5c61]">
                            <span>📍 {{ $row->location }}</span>
                            <span class="font-semibold">{{ $row->orders_count }} Transaksi</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-[#5b5c61]">
                        Tidak ada data penjualan pada periode yang dipilih.
                    </div>
                @endforelse

                @if($stallsSummary->isNotEmpty())
                    <div class="p-4 bg-gray-50 flex items-center justify-between border-t border-gray-200">
                        <span class="text-xs font-bold text-[#1a1c1d] uppercase">TOTAL ({{ $totalOrdersAll }} Pesanan):</span>
                        <span class="text-sm font-black text-[#ed1e28]">Rp {{ number_format($totalRevenueAll, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>
        </div>

    </main>

    <!-- Institutional Footer -->
    <footer class="bg-white border-t border-gray-100 py-4 px-6 text-center text-xs text-[#5b5c61]">
        Universitas Telkom • SMPEK Kantin Terpadu
    </footer>

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
@endsection
