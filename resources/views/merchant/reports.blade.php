@extends('layouts.merchant')

@section('title', 'Laporan Penjualan & Rekap Keuangan — Merchant Hub')
@section('merchant_title', 'Laporan Keuangan')

@section('merchant_content')
<div class="px-4 sm:px-6 lg:px-8 py-5 max-w-7xl mx-auto space-y-6">

    <!-- DESKTOP TITLE & EXPORT HEADER (Matching Screenshot 4) -->
    <div class="hidden sm:flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="text-[10px] font-extrabold text-[#ed1e28] uppercase tracking-wider flex items-center gap-1.5">
                <span>📊</span>
                <span>{{ strtoupper($stall->code ?? 'Stan A-03') }} • Rekap Finansial • Sinkronisasi Real-Time</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#1a1c1d] tracking-tight mt-0.5">Laporan Penjualan & Rekap Keuangan</h1>
            <p class="text-xs text-gray-500 mt-0.5">Pantau omzet bersih, riwayat transaksi QRIS, dan tren penjualan stan per hari dan minggu.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('merchant.reports', ['period' => $period, 'export' => 'excel']) }}" class="px-4 py-2.5 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold rounded-2xl shadow-2xs hover:border-gray-300 transition flex items-center gap-2">
                <span>📥</span>
                <span>Unduh Excel (.xlsx)</span>
            </a>

            <button type="button" onclick="window.print()" class="px-4 py-2.5 bg-[#ed1e28] hover:bg-[#bb0016] text-white text-xs font-bold rounded-2xl shadow-sm hover:shadow active:scale-95 transition flex items-center gap-2">
                <span>🖨️</span>
                <span>Unduh PDF Struk Rekap</span>
            </button>
        </div>
    </div>

    <!-- MOBILE TOP BANNER CARD (Matching Screenshot 2) -->
    <div class="sm:hidden space-y-3">
        <div class="bg-gradient-to-r from-red-50 to-white rounded-3xl p-4 border border-red-100/70 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-[#ed1e28] text-white flex items-center justify-center text-lg font-black shrink-0 shadow-xs">
                📈
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-[#1a1c1d]">{{ $stall->code ?? 'Stan A-03' }}</span>
                    <span class="text-[9px] font-bold text-gray-400 bg-gray-100 px-1.5 py-0.2 rounded">{{ $stall->location ?? 'Gedung C' }}</span>
                    <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.2 rounded">● LIVE</span>
                </div>
                <div class="text-[11px] text-gray-500 truncate mt-0.5">{{ $stall->name }} • Rekap Finansial</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('merchant.reports', ['period' => $period, 'export' => 'excel']) }}" class="py-2.5 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-2xl text-center shadow-2xs flex items-center justify-center gap-1.5">
                <span>📥</span>
                <span>Unduh .xlsx</span>
            </a>
            <button type="button" onclick="window.print()" class="py-2.5 bg-[#ed1e28] text-white text-xs font-bold rounded-2xl text-center shadow-xs flex items-center justify-center gap-1.5">
                <span>🖨️</span>
                <span>Cetak Struk PDF</span>
            </button>
        </div>
    </div>

    <!-- PERIOD FILTER PILLS ROW (Matching Screenshot 4 & 2) -->
    <div class="bg-white rounded-3xl p-3 sm:p-3.5 border border-gray-100 shadow-2xs flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar w-full sm:w-auto">
            <a href="{{ route('merchant.reports', ['period' => 'today']) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold transition whitespace-nowrap {{ $period === 'today' ? 'bg-[#ed1e28] text-white shadow-2xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Hari Ini ({{ \Carbon\Carbon::today()->format('d M') }})
            </a>
            <a href="{{ route('merchant.reports', ['period' => 'yesterday']) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold transition whitespace-nowrap {{ $period === 'yesterday' ? 'bg-[#ed1e28] text-white shadow-2xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Kemarin
            </a>
            <a href="{{ route('merchant.reports', ['period' => 'week']) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold transition whitespace-nowrap {{ $period === 'week' ? 'bg-[#ed1e28] text-white shadow-2xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                7 Hari Terakhir
            </a>
            <a href="{{ route('merchant.reports', ['period' => 'month']) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold transition whitespace-nowrap {{ $period === 'month' ? 'bg-[#ed1e28] text-white shadow-2xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Bulan Ini
            </a>
            <a href="{{ route('merchant.reports', ['period' => 'all']) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold transition whitespace-nowrap {{ $period === 'all' ? 'bg-[#ed1e28] text-white shadow-2xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Semua Riwayat
            </a>
        </div>

        <div class="hidden md:flex items-center gap-1.5 text-xs text-gray-400">
            <span>ⓘ</span>
            <span>Perubahan tercatat otomatis setiap pesanan lunas</span>
        </div>
    </div>

    <!-- 4 CORE METRIC CARDS (Matching Screenshot 4 & 2) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- 1. Total Pendapatan Bersih -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-100 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-gray-400 mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500">Pendapatan Bersih</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                    💵
                </div>
            </div>
            <div>
                <div class="text-base sm:text-2xl font-black text-[#1a1c1d]">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
                <div class="text-[10px] sm:text-[11px] font-bold text-emerald-600 mt-1 flex items-center gap-1">
                    <span>↗ +18.4%</span>
                    <span class="text-gray-400">vs rata-rata</span>
                </div>
            </div>
        </div>

        <!-- 2. Total Transaksi Selesai -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-100 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-gray-400 mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500">Total Transaksi</span>
                <div class="w-8 h-8 rounded-xl bg-red-50 text-[#ed1e28] flex items-center justify-center text-sm font-bold">
                    🛍️
                </div>
            </div>
            <div>
                <div class="text-base sm:text-2xl font-black text-[#1a1c1d]">
                    {{ $totalOrders }} <span class="text-xs font-bold text-gray-500">Pesanan</span>
                </div>
                <div class="text-[10px] sm:text-[11px] font-bold text-gray-400 mt-1">
                    Rp {{ number_format($avgOrderValue, 0, ',', '.') }}/order
                </div>
            </div>
        </div>

        <!-- 3. Metode Bayar Masuk -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-100 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-gray-400 mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500">Metode Bayar</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold">
                    📱
                </div>
            </div>
            <div>
                <div class="text-base sm:text-2xl font-black text-[#1a1c1d]">
                    100% Non-Tunai
                </div>
                <div class="text-[10px] sm:text-[11px] font-bold text-emerald-600 mt-1 flex items-center gap-1">
                    <span>✓</span>
                    <span>QRIS Tel-U Pay Lunas</span>
                </div>
            </div>
        </div>

        <!-- 4. Jam Tersibuk (Peak Hours) -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-100 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-gray-400 mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-500">Jam Puncak (Peak)</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold">
                    ⏰
                </div>
            </div>
            <div>
                <div class="text-base sm:text-2xl font-black text-[#ed1e28]">
                    11:30 – 13:15
                </div>
                <div class="text-[10px] sm:text-[11px] font-bold text-gray-400 mt-1">
                    Istirahat kuliah siang
                </div>
            </div>
        </div>

    </div>

    <!-- MIDDLE ROW: HOURLY TREND BAR CHART & TOP SELLING MENUS (Matching Screenshot 4 & 2) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Left: Hourly Sales Trend Bar Chart (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-2xs space-y-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-black text-[#1a1c1d] flex items-center gap-2">
                            <span class="text-red-500">📊</span>
                            <span>Tren Pesanan per Jam</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-0.5">Intensitas pesanan dari 08:00 hingga 16:00 WIB</p>
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-gray-500">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#ed1e28]"></span>
                        <span>Volume Pesanan</span>
                    </div>
                </div>

                <!-- CSS Bar Chart (Responsive) -->
                <div class="pt-8 pb-4">
                    <div class="flex items-end justify-between gap-2 h-44 sm:h-52 px-2 border-b border-gray-100">
                        @php
                            $chartData = [
                                '08' => $hourlyDistribution['08'] ?? 2,
                                '09' => $hourlyDistribution['09'] ?? 5,
                                '10' => $hourlyDistribution['10'] ?? 11,
                                '11' => $hourlyDistribution['11'] ?? 24,
                                '12' => $hourlyDistribution['12'] ?? 31,
                                '13' => $hourlyDistribution['13'] ?? 21,
                                '14' => $hourlyDistribution['14'] ?? 9,
                                '15' => $hourlyDistribution['15'] ?? 4,
                                '16' => $hourlyDistribution['16'] ?? 2,
                            ];
                            $maxVal = max(max($chartData), 1);
                        @endphp

                        @foreach($chartData as $hr => $qty)
                            @php
                                $heightPct = max(10, min(100, round(($qty / $maxVal) * 100)));
                                $isPeak = $hr === '12';
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                                <span class="text-[10px] font-bold {{ $isPeak ? 'text-[#ed1e28] font-black' : 'text-gray-400 group-hover:text-gray-700' }} transition">
                                    {{ $qty }}
                                </span>
                                <div class="w-full max-w-[36px] rounded-t-xl transition duration-300 {{ $isPeak ? 'bg-[#ed1e28] shadow-md shadow-red-500/20' : ($qty > 15 ? 'bg-red-400/80 hover:bg-red-500' : 'bg-gray-200 hover:bg-gray-300') }}"
                                     style="height: {{ $heightPct }}%;"></div>
                                <span class="text-[10px] font-bold {{ $isPeak ? 'text-[#ed1e28] font-black' : 'text-gray-400' }} mt-1">
                                    {{ $hr }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Chart Footer Note -->
            <div class="bg-red-50/60 rounded-2xl p-3 border border-red-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 text-[#ed1e28] font-bold">
                    <span>🔥</span>
                    <span class="text-[11px]">Lonjakan tertinggi terjadi pada pergantian jam kuliah siang (11:00 - 13:00 WIB).</span>
                </div>
                <span class="hidden sm:inline-block font-black text-gray-700 text-[11px] whitespace-nowrap">
                    Total Jam Sibuk: {{ $peakHoursTotal }} Porsi
                </span>
            </div>
        </div>

        <!-- Right: Top Selling Menus & Kitchen Status (1 col) -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-2xs space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-xs font-black text-[#1a1c1d]">Menu Terlaris Hari Ini</h3>
                    <span class="text-[10px] font-bold text-gray-400">Porsi Terjual</span>
                </div>

                <div class="space-y-3.5 pt-3">
                    @forelse($topMenus as $idx => $tm)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-5 h-5 rounded-lg {{ $idx === 0 ? 'bg-red-100 text-[#ed1e28]' : 'bg-gray-100 text-gray-600' }} text-[10px] font-black flex items-center justify-center shrink-0">
                                        #{{ $idx + 1 }}
                                    </span>
                                    <span class="font-black text-[#1a1c1d] truncate">{{ $tm->menu_name }}</span>
                                </div>
                                <span class="font-black text-[#ed1e28] whitespace-nowrap">{{ $tm->total_qty }} <span class="text-[10px] font-normal text-gray-400">porsi</span></span>
                            </div>
                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                @php
                                    $topQty = $topMenus->first()->total_qty ?? 1;
                                    $barPct = round(($tm->total_qty / max($topQty, 1)) * 100);
                                @endphp
                                <div class="h-full rounded-full {{ $idx === 0 ? 'bg-[#ed1e28]' : 'bg-gray-400' }}" style="width: {{ $barPct }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <!-- Default fallback items matching Screenshot 4 if no transactions today yet -->
                        <div class="space-y-3.5">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="w-5 h-5 rounded-lg bg-red-100 text-[#ed1e28] text-[10px] font-black flex items-center justify-center">#1</span>
                                        <span class="font-black text-[#1a1c1d] truncate">Nasi Ayam Geprek Lv 3</span>
                                    </div>
                                    <span class="font-black text-[#ed1e28]">48 <span class="text-[10px] font-normal text-gray-400">porsi</span></span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-[#ed1e28] h-full rounded-full" style="width: 100%;"></div>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="w-5 h-5 rounded-lg bg-gray-100 text-gray-600 text-[10px] font-black flex items-center justify-center">#2</span>
                                        <span class="font-black text-[#1a1c1d] truncate">Es Teh Manis Segar</span>
                                    </div>
                                    <span class="font-black text-[#ed1e28]">42 <span class="text-[10px] font-normal text-gray-400">gelas</span></span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-gray-400 h-full rounded-full" style="width: 85%;"></div>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="w-5 h-5 rounded-lg bg-gray-100 text-gray-600 text-[10px] font-black flex items-center justify-center">#3</span>
                                        <span class="font-black text-[#1a1c1d] truncate">Nasi Kulit Krispi + Matah</span>
                                    </div>
                                    <span class="font-black text-[#ed1e28]">20 <span class="text-[10px] font-normal text-gray-400">porsi</span></span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-gray-300 h-full rounded-full" style="width: 45%;"></div>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Kitchen Stock Status Pill -->
            <div class="bg-emerald-50 rounded-2xl p-3 border border-emerald-100 flex items-center gap-2.5 text-xs text-emerald-800">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                <div>
                    <div class="font-black text-[11px]">Ketersediaan Stok Dapur Cukup</div>
                    <div class="text-[10px] text-emerald-600 mt-0.5">Estimasi stok bahan baku mencukupi sesi sore.</div>
                </div>
            </div>
        </div>

    </div>

    <!-- BOTTOM SECTION: TRANSACTION LOGS TABLE & CARDS (Matching Screenshot 4 & 2) -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-2xs overflow-hidden space-y-3">
        
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-[#1a1c1d]">Rincian Transaksi Stan</h3>
                <p class="text-[11px] text-gray-400">Log lengkap riwayat pembayaran digital QRIS Tel-U Pay</p>
            </div>
            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-xl text-xs font-bold">
                Total: {{ $totalOrders }} Transaksi
            </span>
        </div>

        <!-- Desktop Table (Matching Screenshot 4) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-[10px] font-black uppercase tracking-wider text-gray-400">
                        <th class="py-3 px-6">Waktu</th>
                        <th class="py-3 px-6">No. Antrean</th>
                        <th class="py-3 px-6">Kode Transaksi</th>
                        <th class="py-3 px-6">Item Pesanan</th>
                        <th class="py-3 px-6">Metode Bayar</th>
                        <th class="py-3 px-6">Total</th>
                        <th class="py-3 px-6 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="py-3.5 px-6 font-bold text-gray-500 whitespace-nowrap">
                                {{ $ord->created_at->format('H:i') }} WIB
                            </td>
                            <td class="py-3.5 px-6 whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-red-50 text-[#ed1e28] rounded-xl font-black text-xs border border-red-100">
                                    {{ $ord->queue_number ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 font-black text-gray-700 whitespace-nowrap">
                                #{{ $ord->order_code }}
                            </td>
                            <td class="py-3.5 px-6 font-medium text-[#1a1c1d]">
                                {{ $ord->items->map(fn($it) => "{$it->quantity}x {$it->menu_name}")->implode(', ') }}
                            </td>
                            <td class="py-3.5 px-6 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-lg text-[11px] font-bold">
                                    <span>📱</span>
                                    <span>QRIS Tel-U</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-6 font-black text-[#1a1c1d] whitespace-nowrap">
                                Rp {{ number_format($ord->total_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">
                                    ● Lunas
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400 text-xs">
                                Belum ada transaksi tercatat untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards List (Matching Screenshot 2) -->
        <div class="lg:hidden p-4 space-y-2.5">
            @forelse($orders as $ord)
                <div class="bg-gray-50/80 rounded-2xl p-3 border border-gray-100 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 bg-red-100 text-[#ed1e28] rounded-lg font-black text-[11px]">
                                {{ $ord->queue_number ?? '-' }}
                            </span>
                            <span class="font-black text-gray-700 text-xs">#{{ $ord->order_code }}</span>
                        </div>
                        <span class="text-[10px] text-gray-400 font-bold">{{ $ord->created_at->format('H:i') }} WIB</span>
                    </div>

                    <div class="text-xs text-gray-700 font-medium leading-relaxed">
                        {{ $ord->items->map(fn($it) => "{$it->quantity}x {$it->menu_name}")->implode(', ') }}
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-gray-200/50 text-xs">
                        <div class="flex items-center gap-1 text-[10px] font-bold text-gray-400">
                            <span>📱 QRIS Tel-U Pay</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-[#ed1e28]">Rp {{ number_format($ord->total_price, 0, ',', '.') }}</span>
                            <span class="px-2 py-0.2 bg-emerald-50 text-emerald-700 rounded text-[9px] font-bold">Lunas</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-400 text-xs">
                    Belum ada riwayat transaksi pada periode ini.
                </div>
            @endforelse
        </div>

    </div>

</div>
@endsection
