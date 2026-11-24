<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stall;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MerchantController extends Controller
{
    protected function getStall(): Stall
    {
        $user = Auth::user();
        $stall = $user->stall;

        if (! $stall) {
            // Fallback or create default stall for seller
            $stall = Stall::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => 'Stan '.$user->name,
                    'code' => 'A-01',
                    'location' => 'Kantin Tel-U Lantai 1',
                    'is_open' => true,
                    'rating' => 4.9,
                    'estimated_time' => '10-15 mnt',
                ]
            );
        }

        return $stall;
    }

    public function dashboard(Request $request): View
    {
        $stall = $this->getStall();
        $tab = $request->input('tab', 'proses'); // 'proses', 'konfirmasi', 'selesai', 'batal'

        $today = Carbon::today()->toDateString();

        // Metrics
        $todayOrders = Order::where('stall_id', $stall->id)
            ->whereDate('created_at', $today);

        $ordersCount = (clone $todayOrders)->whereIn('status', ['diproses', 'siap_diambil', 'selesai'])->count();
        $revenue = (clone $todayOrders)->where('payment_status', 'lunas')->whereIn('status', ['diproses', 'siap_diambil', 'selesai'])->sum('total_price');
        $activeQueuesCount = Order::where('stall_id', $stall->id)->whereIn('status', ['diproses', 'siap_diambil'])->count();

        // Orders by status
        $pendingOrders = Order::with(['user', 'items.menu'])
            ->where('stall_id', $stall->id)
            ->where('status', 'menunggu')
            ->latest()
            ->get();

        $inProcessOrders = Order::with(['user', 'items.menu'])
            ->where('stall_id', $stall->id)
            ->whereIn('status', ['diproses', 'siap_diambil'])
            ->orderBy('id', 'asc')
            ->get();

        $completedOrders = Order::with(['user', 'items.menu'])
            ->where('stall_id', $stall->id)
            ->where('status', 'selesai')
            ->whereDate('created_at', $today)
            ->latest()
            ->get();

        $cancelledOrders = Order::with(['user', 'items.menu'])
            ->where('stall_id', $stall->id)
            ->where('status', 'batal')
            ->whereDate('created_at', $today)
            ->latest()
            ->get();

        return view('merchant.dashboard', compact(
            'stall',
            'tab',
            'ordersCount',
            'revenue',
            'activeQueuesCount',
            'pendingOrders',
            'inProcessOrders',
            'completedOrders',
            'cancelledOrders'
        ));
    }

    public function confirmPayment(Request $request, int $id): RedirectResponse
    {
        $stall = $this->getStall();

        return DB::transaction(function () use ($stall, $id) {
            $order = Order::where('stall_id', $stall->id)
                ->where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($order->status !== 'menunggu') {
                return back()->with('error', 'Status pesanan tidak dalam antrean konfirmasi.');
            }

            // Generate Sequential Queue Number for this stall today (Req. SMPEK-410)
            $today = Carbon::today()->toDateString();
            $lastOrderWithQueue = Order::where('stall_id', $stall->id)
                ->where('order_date', $today)
                ->whereNotNull('queue_number')
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $nextNumber = 1;
            if ($lastOrderWithQueue && preg_match('/[A-Z]-(\d+)/', $lastOrderWithQueue->queue_number, $matches)) {
                $nextNumber = ((int) $matches[1]) + 1;
            }

            $prefix = $stall->code ? explode('-', $stall->code)[0] : 'A';
            $queueNumber = sprintf('%s-%03d', $prefix, $nextNumber);

            // Deduct menu stocks consistently
            foreach ($order->items as $item) {
                if ($item->menu_id) {
                    $menu = Menu::where('id', $item->menu_id)->lockForUpdate()->first();
                    if ($menu) {
                        $menu->stock = max(0, $menu->stock - $item->quantity);
                        if ($menu->stock === 0) {
                            $menu->is_available = false;
                        }
                        $menu->save();
                    }
                }
            }

            $order->update([
                'status' => 'diproses',
                'payment_status' => 'lunas',
                'payment_time' => Carbon::now(),
                'queue_number' => $queueNumber,
                'order_date' => $today,
                'estimated_ready_time' => 'Perkiraan siap dalam ~6 Menit',
            ]);

            // Notify buyer
            Notification::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'title' => 'Pembayaran Dikonfirmasi!',
                'message' => "Pesanan {$order->order_code} dikonfirmasi oleh {$stall->name}. Nomor antrean Anda: {$queueNumber}.",
            ]);

            return back()->with('success', "Pembayaran dikonfirmasi! Nomor antrean {$queueNumber} berhasil diterbitkan.");
        });
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $stall = $this->getStall();
        $order = Order::where('stall_id', $stall->id)->findOrFail($id);

        $newStatus = $request->input('status'); // 'diproses', 'siap_diambil', 'selesai', 'batal'

        // Enforce sequential transitions (Req. SMPEK-520)
        $validTransition = match ($order->status) {
            'menunggu' => in_array($newStatus, ['diproses', 'batal']),
            'diproses' => in_array($newStatus, ['siap_diambil', 'batal']),
            'siap_diambil' => in_array($newStatus, ['selesai']),
            default => false,
        };

        if (! $validTransition && $newStatus !== 'batal') {
            return back()->with('error', 'Pembaruan status harus berurutan: Menunggu → Diproses → Siap Diambil → Selesai.');
        }

        $order->status = $newStatus;

        if ($newStatus === 'siap_diambil') {
            $order->estimated_ready_time = 'Pesanan siap diambil di meja etalase';
            // Req. SMPEK-430: Notifikasi Siap Ambil
            Notification::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'title' => 'Pesanan Siap Diambil! 🔔',
                'message' => "Antrean {$order->queue_number} siap diambil di stan {$stall->name}!",
            ]);
        } elseif ($newStatus === 'selesai') {
            $order->estimated_ready_time = 'Pesanan telah selesai diambil';
        }

        $order->save();

        $statusMsg = match ($newStatus) {
            'siap_diambil' => 'Status diubah menjadi Siap Diambil. Notifikasi telah dikirim ke pembeli!',
            'selesai' => 'Pesanan selesai dilayani.',
            'batal' => 'Pesanan dibatalkan.',
            default => 'Status pesanan diperbarui.',
        };

        return back()->with('success', $statusMsg);
    }

    public function completeAllReadyOrders(): RedirectResponse
    {
        $stall = $this->getStall();

        $updatedCount = Order::where('stall_id', $stall->id)
            ->where('status', 'siap_diambil')
            ->update([
                'status' => 'selesai',
                'estimated_ready_time' => 'Pesanan telah selesai diambil di etalase',
            ]);

        return back()->with('success', "{$updatedCount} pesanan siap di etalase berhasil diselesaikan sekaligus.");
    }

    public function menus(): View
    {
        $stall = $this->getStall();
        $categories = Category::all();
        $menus = Menu::with('category')->where('stall_id', $stall->id)->latest()->get();

        $totalMenus = $menus->count();
        $availableMenus = $menus->filter(fn ($m) => $m->stock > 0 && $m->is_available)->count();
        $outOfStockMenus = $totalMenus - $availableMenus;
        $criticalMenus = $menus->filter(fn ($m) => $m->stock > 0 && $m->stock <= 5)->count();

        // Best seller today
        $today = Carbon::today();
        $bestSellerItem = OrderItem::whereHas('order', function ($q) use ($stall, $today) {
            $q->where('stall_id', $stall->id)
                ->where('payment_status', 'lunas')
                ->whereDate('created_at', $today);
        })
            ->select('menu_name', DB::raw('SUM(quantity) as qty'))
            ->groupBy('menu_name')
            ->orderByDesc('qty')
            ->first();

        $bestSellerName = $bestSellerItem ? $bestSellerItem->menu_name : ($menus->first()->name ?? 'Nasi Ayam Geprek Lv 3');
        $bestSellerQty = $bestSellerItem ? (int) $bestSellerItem->qty : 48;

        return view('merchant.menus', compact(
            'stall',
            'categories',
            'menus',
            'totalMenus',
            'availableMenus',
            'outOfStockMenus',
            'criticalMenus',
            'bestSellerName',
            'bestSellerQty'
        ));
    }

    public function clearAllStock(): RedirectResponse
    {
        $stall = $this->getStall();
        Menu::where('stall_id', $stall->id)->update([
            'stock' => 0,
            'is_available' => false,
        ]);

        return back()->with('success', 'Semua stok menu hari ini berhasil dihabiskan / dinonaktifkan.');
    }

    public function storeMenu(Request $request): RedirectResponse
    {
        $stall = $this->getStall();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'prep_time' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'has_spice_levels' => ['nullable', 'boolean'],
            'has_jumbo_portion' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama menu wajib diisi.',
            'category_id.required' => 'Kategori menu wajib dipilih.',
            'price.required' => 'Harga wajib diisi.',
            'price.min' => 'Harga tidak boleh negatif.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.min' => 'Stok tidak boleh negatif.',
            'photo.image' => 'File poster/foto harus berupa gambar valid (JPG, PNG, WebP).',
            'photo.max' => 'Ukuran poster/foto maksimal 5MB.',
        ]);

        $photo = 'assets/figma/6a55e.png'; // default fallback photo
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('menus', 'public');
            $photo = 'storage/'.$path;
        }

        // Minuman does not have spice level (Req: minuman tidak ada level)
        $category = Category::find($request->input('category_id'));
        $isDrink = $category && (strtolower($category->slug) === 'minuman' || str_contains(strtolower($category->name), 'minum'));

        $spiceLevels = null;
        if (! $isDrink && $request->boolean('has_spice_levels', true)) {
            if ($request->filled('custom_spice_levels')) {
                $rawLevels = array_map('trim', explode(',', $request->input('custom_spice_levels')));
                $spiceLevels = array_values(array_filter($rawLevels));
            } else {
                $spiceLevels = ['Level 1 Santai', 'Level 3 Sedang', 'Level 5 Nampol 🔥'];
            }
        }

        $portionOptions = null;
        if ($request->boolean('has_jumbo_portion', true)) {
            $portionOptions = [
                ['name' => 'Porsi Reguler', 'extra_price' => 0],
                ['name' => 'Porsi Jumbo', 'extra_price' => 3000],
            ];
        }

        Menu::create([
            'stall_id' => $stall->id,
            'category_id' => $request->input('category_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'photo' => $photo,
            'stock' => $request->input('stock'),
            'is_available' => $request->integer('stock') > 0,
            'prep_time' => $request->input('prep_time', '10 mnt'),
            'spice_levels' => $spiceLevels,
            'portion_options' => $portionOptions,
        ]);

        return back()->with('success', 'Menu baru berhasil ditambahkan.');
    }

    public function updateMenu(Request $request, int $id): RedirectResponse
    {
        $stall = $this->getStall();
        $menu = Menu::where('stall_id', $stall->id)->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'prep_time' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'has_spice_levels' => ['nullable', 'boolean'],
            'has_jumbo_portion' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('photo')) {
            if ($menu->photo && str_starts_with($menu->photo, 'storage/')) {
                $oldPath = str_replace('storage/', '', $menu->photo);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('photo')->store('menus', 'public');
            $menu->photo = 'storage/'.$path;
        }

        // Minuman does not have spice level
        $category = Category::find($request->input('category_id'));
        $isDrink = $category && (strtolower($category->slug) === 'minuman' || str_contains(strtolower($category->name), 'minum'));

        $spiceLevels = null;
        if (! $isDrink && $request->boolean('has_spice_levels', true)) {
            if ($request->filled('custom_spice_levels')) {
                $rawLevels = array_map('trim', explode(',', $request->input('custom_spice_levels')));
                $spiceLevels = array_values(array_filter($rawLevels));
            } else {
                $spiceLevels = ['Level 1 Santai', 'Level 3 Sedang', 'Level 5 Nampol 🔥'];
            }
        }

        $portionOptions = null;
        if ($request->boolean('has_jumbo_portion', true)) {
            $portionOptions = [
                ['name' => 'Porsi Reguler', 'extra_price' => 0],
                ['name' => 'Porsi Jumbo', 'extra_price' => 3000],
            ];
        }

        $stock = $request->integer('stock');

        $menu->update([
            'category_id' => $request->input('category_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'stock' => $stock,
            'is_available' => $stock > 0,
            'prep_time' => $request->input('prep_time', $menu->prep_time),
            'spice_levels' => $spiceLevels,
            'portion_options' => $portionOptions,
        ]);

        return back()->with('success', 'Menu berhasil diperbarui.');
    }

    public function updateStock(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $stall = $this->getStall();
        $menu = Menu::where('stall_id', $stall->id)->findOrFail($id);

        if ($request->has('stock')) {
            $stock = max(0, $request->integer('stock'));
            $menu->stock = $stock;
            $menu->is_available = $stock > 0;
            $menu->save();
        } elseif ($request->has('toggle_available')) {
            $menu->is_available = ! $menu->is_available;
            $menu->save();
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'stock' => $menu->stock, 'is_available' => $menu->is_available]);
        }

        return back()->with('success', 'Ketersediaan menu diperbarui.');
    }

    public function deleteMenu(int $id): RedirectResponse
    {
        $stall = $this->getStall();
        $menu = Menu::where('stall_id', $stall->id)->findOrFail($id);

        if ($menu->photo && str_starts_with($menu->photo, 'storage/')) {
            $oldPath = str_replace('storage/', '', $menu->photo);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus.');
    }

    public function reports(Request $request): View|StreamedResponse
    {
        $stall = $this->getStall();
        $period = $request->input('period', 'today'); // 'today', 'yesterday', 'week', 'month', 'all'

        $startDate = match ($period) {
            'yesterday' => Carbon::yesterday()->startOfDay(),
            'week' => Carbon::now()->subDays(7)->startOfDay(),
            'month' => Carbon::now()->subDays(30)->startOfDay(),
            'all' => Carbon::now()->subYears(2)->startOfDay(),
            default => Carbon::today()->startOfDay(),
        };

        $endDate = match ($period) {
            'yesterday' => Carbon::yesterday()->endOfDay(),
            default => Carbon::now()->endOfDay(),
        };

        $orders = Order::with(['items', 'user'])
            ->where('stall_id', $stall->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'lunas')
            ->whereIn('status', ['diproses', 'siap_diambil', 'selesai'])
            ->latest()
            ->get();

        $grossRevenue = $orders->sum('subtotal');
        $totalDiscount = $orders->sum('discount');
        $totalRevenue = $orders->sum('total_price');
        $totalOrders = $orders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Top Selling Menus for this stall
        $topMenus = OrderItem::whereIn('order_id', $orders->pluck('id'))
            ->select('menu_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->groupBy('menu_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        if (in_array($request->input('export'), ['excel', 'csv'])) {
            $cleanStallName = preg_replace('/[^A-Za-z0-9_-]/', '_', $stall->name);
            $fileName = "Laporan-Keuangan-{$cleanStallName}-{$period}-".Carbon::now()->format('Ymd_His').'.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ];

            return response()->stream(function () use ($stall, $orders, $totalRevenue, $totalOrders, $period) {
                $handle = fopen('php://output', 'w');
                // Output UTF-8 BOM for Microsoft Excel compatibility
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                fputcsv($handle, ['LAPORAN KEUANGAN & PENJUALAN STAN KANTIN TELKOM UNIVERSITY']);
                fputcsv($handle, ['Nama Stan:', $stall->name, 'Kode:', $stall->code ?? '-', 'Lokasi:', $stall->location]);
                fputcsv($handle, ['Periode Laporan:', strtoupper($period), 'Waktu Unduh:', Carbon::now()->toDateTimeString()]);
                fputcsv($handle, []);
                fputcsv($handle, ['No', 'Kode Transaksi', 'Antrean', 'Waktu Transaksi', 'Nama Pembeli', 'Menu Hidangan', 'Jumlah Porsi', 'Metode Bayar', 'Status Bayar', 'Total Tagihan (IDR)']);

                foreach ($orders as $idx => $ord) {
                    $menuList = $ord->items->map(fn ($it) => "{$it->quantity}x {$it->menu_name}")->implode('; ');
                    $qtyTotal = $ord->items->sum('quantity');

                    fputcsv($handle, [
                        $idx + 1,
                        $ord->order_code,
                        $ord->queue_number ?? '-',
                        $ord->created_at->format('d/m/Y H:i'),
                        $ord->user->name ?? 'Mahasiswa',
                        $menuList,
                        $qtyTotal,
                        'QRIS Tel-U Pay',
                        'LUNAS',
                        $ord->total_price,
                    ]);
                }

                fputcsv($handle, []);
                fputcsv($handle, ['', '', '', '', '', 'TOTAL PESANAN BERHASIL', $totalOrders, '', 'TOTAL OMZET (IDR)', $totalRevenue]);
                fclose($handle);
            }, 200, $headers);
        }

        // Hourly breakdown for chart (08:00 - 16:00)
        $hourlyDistribution = [
            '08' => 0, '09' => 0, '10' => 0, '11' => 0,
            '12' => 0, '13' => 0, '14' => 0, '15' => 0, '16' => 0,
        ];
        foreach ($orders as $ord) {
            $h = $ord->created_at->format('H');
            if (isset($hourlyDistribution[$h])) {
                $hourlyDistribution[$h] += $ord->items->sum('quantity') ?: 1;
            }
        }
        $maxHourly = max(max($hourlyDistribution), 1);
        $peakHoursTotal = ($hourlyDistribution['11'] ?? 0) + ($hourlyDistribution['12'] ?? 0) + ($hourlyDistribution['13'] ?? 0);
        if ($peakHoursTotal === 0 && $totalOrders > 0) {
            $peakHoursTotal = $totalOrders;
        }

        return view('merchant.reports', compact(
            'stall',
            'period',
            'orders',
            'totalRevenue',
            'grossRevenue',
            'totalDiscount',
            'totalOrders',
            'avgOrderValue',
            'topMenus',
            'hourlyDistribution',
            'maxHourly',
            'peakHoursTotal'
        ));
    }

    public function toggleStallStatus(): RedirectResponse
    {
        $stall = $this->getStall();
        $stall->is_open = ! $stall->is_open;
        $stall->save();

        $msg = $stall->is_open ? 'Stan sekarang DIBUKA untuk menerima pesanan.' : 'Stan sekarang DITUTUP.';

        return back()->with('success', $msg);
    }

    public function stallProfile(): View
    {
        $stall = $this->getStall();

        $staffMembers = [
            [
                'name' => $stall->user->name ?? 'Ibu Siti Rahayu',
                'email' => $stall->user->email ?? 'sitirahayu@mitra.telkomuniversity.ac.id',
                'role_label' => 'Pemilik Stan (Master Admin)',
                'is_owner' => true,
                'status' => 'Aktif Bertugas',
            ],
            [
                'name' => 'Dimas Pratama',
                'email' => 'dimas.kasir03@gmail.com',
                'role_label' => 'Kasir Stan',
                'is_owner' => false,
                'status' => 'Aktif Bertugas',
            ],
            [
                'name' => 'Ahmad Rofi',
                'email' => 'ahmad.kitchen03@gmail.com',
                'role_label' => 'Juru Masak (Display Dapur)',
                'is_owner' => false,
                'status' => 'Shift Sore (13.00)',
            ],
        ];

        return view('merchant.stall_profile', compact('stall', 'staffMembers'));
    }

    public function updateStallProfile(Request $request): RedirectResponse
    {
        $stall = $this->getStall();

        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:255'],
            'opening_hours' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'bank_info' => ['nullable', 'string', 'max:255'],
            'culinary_category' => ['nullable', 'string', 'max:255'],
            'max_active_queues' => ['nullable', 'integer', 'min:1'],
            'sla_minutes' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'owner_phone' => ['nullable', 'string', 'max:20'],
            'owner_password' => ['nullable', 'string', 'min:6'],
            'avatar' => ['nullable', 'image', 'max:5120'],
        ], [
            'image.image' => 'File foto stan harus berupa gambar valid.',
            'avatar.image' => 'File foto profil pemilik harus berupa gambar valid.',
        ]);

        $data = [
            'name' => $request->input('name', $stall->name),
            'code' => $request->input('code') ?: $stall->code,
            'location' => $request->input('location', $stall->location),
            'opening_hours' => $request->input('opening_hours', $stall->opening_hours ?? '07:30 - 17:00 WIB'),
            'contact_phone' => $request->input('contact_phone', $stall->contact_phone),
            'culinary_category' => $request->input('culinary_category', $stall->culinary_category ?? 'Makanan Berat & Olahan Ayam'),
            'max_active_queues' => $request->integer('max_active_queues', $stall->max_active_queues ?: 15),
            'sla_minutes' => $request->integer('sla_minutes', $stall->sla_minutes ?: 8),
            'auto_accept_qris' => $request->has('auto_accept_qris') ? $request->boolean('auto_accept_qris') : ($stall->auto_accept_qris ?? true),
            'sound_notification' => $request->has('sound_notification') ? $request->boolean('sound_notification') : ($stall->sound_notification ?? true),
            'bank_info' => $request->input('bank_info', $stall->bank_info),
            'description' => $request->input('description', $stall->description),
            'is_open' => $request->has('is_open') ? $request->boolean('is_open') : ($stall->is_open ?? true),
        ];

        if ($request->hasFile('image')) {
            if ($stall->image && str_starts_with($stall->image, 'storage/')) {
                $oldPath = str_replace('storage/', '', $stall->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('image')->store('stalls', 'public');
            $data['image'] = 'storage/'.$path;
        }

        $stall->update($data);

        // Update stall owner account biodata if provided
        if ($stall->user) {
            $user = $stall->user;
            if ($request->filled('owner_name')) {
                $user->name = $request->input('owner_name');
            }
            if ($request->filled('owner_phone')) {
                $user->phone = $request->input('owner_phone');
            }
            if ($request->filled('owner_password')) {
                $user->password = Hash::make($request->input('owner_password'));
            }
            if ($request->hasFile('avatar')) {
                if ($user->avatar && str_starts_with($user->avatar, 'storage/')) {
                    $oldAvatar = str_replace('storage/', '', $user->avatar);
                    if (Storage::disk('public')->exists($oldAvatar)) {
                        Storage::disk('public')->delete($oldAvatar);
                    }
                }
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = 'storage/'.$avatarPath;
            }
            $user->save();
        }

        return back()->with('success', 'Informasi dan biodata stan serta akun penjual berhasil diperbarui.');
    }
}
