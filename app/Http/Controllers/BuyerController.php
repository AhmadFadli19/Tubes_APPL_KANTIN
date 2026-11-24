<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SellerRequest;
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

class BuyerController extends Controller
{
    public function onboarding(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'penjual' => redirect()->route('merchant.dashboard'),
                default => redirect()->route('menu.index'),
            };
        }

        return view('buyer.onboarding');
    }

    public function menuCatalog(Request $request): View
    {
        $search = $request->input('q');
        $categorySlug = $request->input('category');
        $stallId = $request->input('stall');

        $categories = Category::all();
        $stalls = Stall::withCount(['menus'])->get();

        $allMenus = Menu::with('stall', 'category')->where('is_available', true)->get();
        $menusQuery = Menu::with('stall', 'category')->where('is_available', true);

        if ($search) {
            $menusQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('stall', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($categorySlug && $categorySlug !== 'semua') {
            $menusQuery->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($stallId) {
            $menusQuery->where('stall_id', $stallId);
        }

        $menus = $menusQuery->get();

        $cartCount = Auth::check() ? CartItem::where('user_id', Auth::id())->sum('quantity') : 0;
        $activeOrder = Auth::check()
            ? Order::where('user_id', Auth::id())
                ->whereIn('status', ['menunggu', 'diproses', 'siap_diambil'])
                ->latest()
                ->first()
            : null;

        return view('buyer.menu', compact('categories', 'stalls', 'menus', 'allMenus', 'search', 'categorySlug', 'stallId', 'cartCount', 'activeOrder'));
    }

    public function menuDetail(int $id): View|JsonResponse
    {
        $menu = Menu::with('stall', 'category')->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json($menu);
        }

        return view('buyer.menu_detail', compact('menu'));
    }

    public function cart(): View|RedirectResponse
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::user()->isPenjual()) {
            return redirect()->route('merchant.dashboard');
        }

        $cartItems = CartItem::with(['menu.stall'])
            ->where('user_id', Auth::id())
            ->get();

        $subtotal = $cartItems->sum(fn ($item) => $item->subtotal);
        $serviceFee = $cartItems->isNotEmpty() ? 1000 : 0;
        $discount = 0; // Fitur diskon ditiadakan sesuai instruksi
        $total = $subtotal + $serviceFee;

        return view('buyer.cart', compact('cartItems', 'subtotal', 'serviceFee', 'discount', 'total'));
    }

    public function addToCart(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'menu_id' => ['required', 'exists:menus,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'spice_level' => ['nullable', 'string'],
            'portion' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $menu = Menu::findOrFail($request->input('menu_id'));

        if (! $menu->is_available || $menu->stock <= 0) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Menu sedang tidak tersedia atau stok habis.'], 422);
            }

            return back()->with('error', 'Menu sedang tidak tersedia atau stok habis.');
        }

        $qtyToAdd = $request->integer('quantity');

        // Check if existing item in cart
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('menu_id', $menu->id)
            ->where('spice_level', $request->input('spice_level'))
            ->where('portion', $request->input('portion'))
            ->first();

        $newQty = ($cartItem ? $cartItem->quantity : 0) + $qtyToAdd;

        if ($newQty > $menu->stock) {
            $msg = "Jumlah melebihi stok yang tersedia (Sisa {$menu->stock} porsi).";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        $extraPrice = 0;
        if ($request->input('portion') && str_contains($request->input('portion'), 'Jumbo')) {
            $extraPrice = 3000;
        }

        if ($cartItem) {
            $cartItem->quantity = $newQty;
            if ($request->filled('notes')) {
                $cartItem->notes = $request->input('notes');
            }
            $cartItem->save();
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'menu_id' => $menu->id,
                'quantity' => $qtyToAdd,
                'spice_level' => $request->input('spice_level'),
                'portion' => $request->input('portion'),
                'extra_price' => $extraPrice,
                'notes' => $request->input('notes'),
            ]);
        }

        $cartCount = CartItem::where('user_id', Auth::id())->sum('quantity');

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Menu berhasil ditambahkan ke keranjang.', 'cart_count' => $cartCount]);
        }

        return redirect()->route('cart.index')->with('success', 'Menu berhasil ditambahkan ke keranjang.');
    }

    public function updateCart(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);
        $action = $request->input('action'); // 'increase', 'decrease', 'set', 'update_note'

        if ($action === 'increase') {
            if ($cartItem->quantity + 1 > $cartItem->menu->stock) {
                return back()->with('error', "Stok maksimal tercapai ({$cartItem->menu->stock} porsi).");
            }
            $cartItem->increment('quantity');
        } elseif ($action === 'decrease') {
            if ($cartItem->quantity > 1) {
                $cartItem->decrement('quantity');
            } else {
                $cartItem->delete();
            }
        } elseif ($action === 'update_note') {
            $cartItem->notes = $request->input('notes');
            $cartItem->save();
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    public function removeFromCart(int $id): RedirectResponse
    {
        CartItem::where('user_id', Auth::id())->where('id', $id)->delete();

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    public function clearCart(): RedirectResponse
    {
        CartItem::where('user_id', Auth::id())->delete();

        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $cartItems = CartItem::with('menu.stall')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('menu.index')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        // Verify stock before checkout
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->menu->stock) {
                return redirect()->route('cart.index')->with('error', "Stok untuk {$item->menu->name} tidak mencukupi (Tersisa: {$item->menu->stock}).");
            }
        }

        // Group by stall or take primary stall
        $primaryStallId = $cartItems->first()->menu->stall_id;

        $subtotal = $cartItems->sum(fn ($item) => $item->subtotal);
        $serviceFee = 1000;
        $discount = 0; // Fitur diskon ditiadakan
        $total = $subtotal + $serviceFee;

        $orderCode = '#TELU-'.rand(1000, 9999);
        while (Order::where('order_code', $orderCode)->exists()) {
            $orderCode = '#TELU-'.rand(1000, 9999);
        }

        $order = Order::create([
            'order_code' => $orderCode,
            'user_id' => Auth::id(),
            'stall_id' => $primaryStallId,
            'queue_number' => null, // Generated upon payment
            'order_date' => Carbon::now()->toDateString(),
            'status' => 'menunggu',
            'payment_status' => 'belum_bayar', // Menunggu scan QRIS
            'payment_method' => 'qris_telu_pay',
            'payment_time' => null,
            'subtotal' => $subtotal,
            'service_fee' => $serviceFee,
            'discount' => $discount,
            'total_price' => $total,
            'notes' => $cartItems->pluck('notes')->filter()->implode(', '),
            'estimated_ready_time' => 'Menunggu scan QRIS',
        ]);

        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $cartItem->menu_id,
                'menu_name' => $cartItem->menu->name,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->menu->price + $cartItem->extra_price,
                'subtotal' => $cartItem->subtotal,
                'spice_level' => $cartItem->spice_level,
                'portion' => $cartItem->portion,
                'notes' => $cartItem->notes,
            ]);
        }

        // Clear cart
        CartItem::where('user_id', Auth::id())->delete();

        return redirect()->route('orders.qris', $order->id);
    }

    public function showQrisPayment(int $id): View|RedirectResponse
    {
        $order = Order::with(['stall', 'items.menu', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($order->payment_status === 'lunas') {
            return redirect()->route('orders.show', $order->id)->with('success', 'Pesanan ini sudah dibayar!');
        }

        $cleanCode = str_replace('#', '', $order->order_code);

        // LAN Wi-Fi IP for direct smartphone scan via Google Lens
        $lanIp = '192.168.100.11';
        $port = request()->getPort() ?: 8000;
        $host = request()->getHost();
        $isLocalhost = in_array($host, ['localhost', '127.0.0.1']);

        // Google Lens needs LAN IP so phone on Wi-Fi can reach the server
        $scanHost = $isLocalhost ? "{$lanIp}:{$port}" : request()->getHttpHost();
        $payUrl = "http://{$scanHost}/pay/{$cleanCode}";
        $localPayUrl = url("/pay/{$cleanCode}");

        return view('buyer.qris_payment', compact('order', 'payUrl', 'localPayUrl', 'cleanCode', 'lanIp'));
    }

    public function processScanPayment(string $orderCode): View|RedirectResponse
    {
        $code = str_starts_with($orderCode, '#') ? $orderCode : '#'.$orderCode;

        $order = Order::with(['stall', 'items.menu', 'user'])
            ->where('order_code', $code)
            ->first();

        if (! $order) {
            $order = Order::with(['stall', 'items.menu', 'user'])
                ->where('order_code', $orderCode)
                ->firstOrFail();
        }

        if ($order->payment_status !== 'lunas') {
            DB::transaction(function () use ($order) {
                $stall = $order->stall;
                $today = Carbon::today()->toDateString();

                // Generate Sequential Queue Number for this stall today
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

                // Deduct menu stocks
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
                    'estimated_ready_time' => 'Sedang dimasak oleh koki stan (~6 Menit)',
                ]);

                // Notify buyer
                Notification::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'title' => 'Pembayaran QRIS Berhasil! 💳',
                    'message' => "Pesanan {$order->order_code} lunas! Nomor antrean Anda: {$queueNumber}. Koki sedang memasak pesanan Anda.",
                ]);
            });

            $order->refresh();
        }

        if (Auth::check() && Auth::id() === $order->user_id) {
            return redirect()->route('orders.show', $order->id)->with('success', "Pembayaran QRIS Berhasil! Nomor antrean Anda: {$order->queue_number}");
        }

        return view('buyer.payment_success', compact('order'));
    }

    public function orderStatus(int $id): View
    {
        $order = Order::with(['stall', 'items.menu', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Calculate remaining queue ahead of this order for this stall today
        $remainingQueue = 0;
        if ($order->queue_number) {
            $remainingQueue = Order::where('stall_id', $order->stall_id)
                ->where('order_date', $order->order_date)
                ->whereIn('status', ['diproses'])
                ->where('id', '<', $order->id)
                ->count();
        }

        return view('buyer.order_status', compact('order', 'remainingQueue'));
    }

    public function orderStatusApi(int $id): JsonResponse
    {
        $order = Order::with('stall')->findOrFail($id);

        $remainingQueue = 0;
        if ($order->queue_number) {
            $remainingQueue = Order::where('stall_id', $order->stall_id)
                ->where('order_date', $order->order_date)
                ->whereIn('status', ['diproses'])
                ->where('id', '<', $order->id)
                ->count();
        }

        return response()->json([
            'status' => $order->status,
            'status_label' => $order->status_label,
            'status_step' => $order->status_step,
            'queue_number' => $order->queue_number ?? '-',
            'remaining_queue' => $remainingQueue,
            'estimated_ready_time' => $order->estimated_ready_time,
            'is_paid' => $order->payment_status === 'lunas',
            'payment_status' => $order->payment_status,
            'is_ready' => $order->status === 'siap_diambil',
            'is_completed' => $order->status === 'selesai',
        ]);
    }

    public function receipt(int $id): View
    {
        $order = Order::with(['stall', 'items', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('buyer.receipt', compact('order'));
    }

    public function orders(): View|RedirectResponse
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.transactions');
        }
        if (Auth::user()->isPenjual()) {
            return redirect()->route('merchant.dashboard');
        }

        $orders = Order::with(['stall', 'items'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('buyer.order_history', compact('orders'));
    }

    public function showSellerRegistration(): View
    {
        $existingRequest = Auth::check()
            ? SellerRequest::where('user_id', Auth::id())->latest()->first()
            : null;

        return view('buyer.seller_register', compact('existingRequest'));
    }

    public function submitSellerRegistration(Request $request): RedirectResponse
    {
        $request->validate([
            'applicant_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'stall_name' => ['required', 'string', 'max:255'],
            'stall_location' => ['required', 'string', 'max:255'],
            'stall_description' => ['required', 'string'],
            'opening_hours' => ['required', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'bank_info' => ['nullable', 'string', 'max:255'],
        ], [
            'applicant_name.required' => 'Nama lengkap pemohon wajib diisi.',
            'email.required' => 'Email aktif wajib diisi.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'stall_name.required' => 'Nama stan / usaha kuliner wajib diisi.',
            'stall_location.required' => 'Preferensi lokasi stan di kantin wajib diisi.',
            'stall_description.required' => 'Deskripsi stan dan menu utama wajib diisi.',
            'opening_hours.required' => 'Rencana jam operasional wajib diisi.',
        ]);

        SellerRequest::create([
            'user_id' => Auth::id(),
            'applicant_name' => $request->input('applicant_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'stall_name' => $request->input('stall_name'),
            'stall_location' => $request->input('stall_location'),
            'stall_description' => $request->input('stall_description'),
            'opening_hours' => $request->input('opening_hours'),
            'contact_phone' => $request->input('contact_phone') ?: $request->input('phone'),
            'bank_info' => $request->input('bank_info'),
            'status' => 'menunggu',
        ]);

        return redirect()->route('seller.register.form')->with('success', 'Permintaan pendaftaran stan penjual berhasil dikirim! Tim Administrator Kantin Tel-U akan meninjau dan mengonfirmasi pengajuan Anda.');
    }

    public function profile(): View
    {
        $user = Auth::user();

        // Orders for this user
        $orders = Order::with(['stall', 'items.menu'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Laporan Pengeluaran (Expense Analysis)
        $paidOrders = $orders->whereIn('payment_status', ['lunas']);
        $totalExpenses = $paidOrders->sum('total_price');

        $today = Carbon::today()->toDateString();
        $todayExpenses = $paidOrders->filter(fn ($o) => Carbon::parse($o->created_at)->toDateString() === $today)->sum('total_price');

        $thisMonth = Carbon::now()->format('Y-m');
        $monthExpenses = $paidOrders->filter(fn ($o) => Carbon::parse($o->created_at)->format('Y-m') === $thisMonth)->sum('total_price');

        $totalOrdersCount = $orders->count();
        $avgExpense = $paidOrders->count() > 0 ? (int) round($totalExpenses / $paidOrders->count()) : 0;

        // Breakdown per stall / favorite stalls
        $spendingByStall = $paidOrders
            ->groupBy('stall_id')
            ->map(function ($stallOrders) {
                $stall = $stallOrders->first()->stall;

                return [
                    'stall_name' => $stall ? $stall->name : 'Kantin Tel-U',
                    'stall_code' => $stall ? $stall->code : '-',
                    'order_count' => $stallOrders->count(),
                    'total_amount' => $stallOrders->sum('total_price'),
                ];
            })
            ->sortByDesc('total_amount');

        return view('buyer.profile', compact(
            'user',
            'orders',
            'totalExpenses',
            'todayExpenses',
            'monthExpenses',
            'totalOrdersCount',
            'avgExpense',
            'spendingByStall'
        ));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username,'.$user->id],
            'phone' => ['required', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'max:5120'],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'avatar.image' => 'File foto profil harus berupa gambar valid (JPG, PNG, WebP).',
            'avatar.max' => 'Ukuran foto profil maksimal 5MB.',
            'current_password.required_with' => 'Kata sandi saat ini diperlukan untuk mengganti kata sandi baru.',
            'new_password.min' => 'Kata sandi baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && str_starts_with($user->avatar, 'storage/')) {
                $oldPath = str_replace('storage/', '', $user->avatar);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = 'storage/'.$path;
        }

        if ($request->filled('username')) {
            $user->username = $request->input('username');
        }

        if ($request->filled('new_password')) {
            if (! Hash::check($request->input('current_password'), $user->password)) {
                return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
            }
            $user->password = Hash::make($request->input('new_password'));
        }

        $user->name = $request->input('name');
        $user->phone = $request->input('phone');
        $user->save();

        return back()->with('success', 'Biodata profil dan foto akun berhasil diperbarui.');
    }
}
