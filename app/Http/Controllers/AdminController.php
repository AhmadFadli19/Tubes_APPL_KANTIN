<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\SellerRequest;
use App\Models\Stall;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function dashboard(Request $request): View
    {
        $roleFilter = $request->input('role', 'all'); // 'all', 'pembeli', 'penjual'
        $search = $request->input('search');

        $usersQuery = User::with('stall')->latest();

        if ($roleFilter !== 'all') {
            $usersQuery->where('role', $roleFilter);
        }

        if ($search) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->paginate(15);

        // Stats
        $totalUsers = User::count();
        $totalStalls = Stall::count();
        $activeBuyers = User::where('role', 'pembeli')->where('status', 'aktif')->count();
        $todayOrdersCount = Order::whereDate('created_at', Carbon::today())->count();

        $availableStalls = Stall::doesntHave('user')->get();
        $allStalls = Stall::all();
        $sellerRequests = SellerRequest::latest()->get();
        $pendingRequestsCount = SellerRequest::where('status', 'menunggu')->count();

        return view('admin.dashboard', compact(
            'users',
            'roleFilter',
            'search',
            'totalUsers',
            'totalStalls',
            'activeBuyers',
            'todayOrdersCount',
            'availableStalls',
            'allStalls',
            'sellerRequests',
            'pendingRequestsCount'
        ));
    }

    public function storeSeller(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
            'stall_id' => ['nullable', 'exists:stalls,id'],
            'new_stall_name' => ['nullable', 'string', 'max:255'],
            'new_stall_code' => ['nullable', 'string', 'max:20'],
            'new_stall_location' => ['nullable', 'string', 'max:255'],
            'new_stall_description' => ['nullable', 'string'],
            'new_stall_opening_hours' => ['nullable', 'string'],
            'new_stall_bank_info' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama pemilik stan wajib diisi.',
            'email.required' => 'Email login wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'password.required' => 'Kata sandi awal wajib diisi.',
        ]);

        $username = explode('@', $request->input('email'))[0];
        $base = $username;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base.$i++;
        }

        $user = User::create([
            'name' => $request->input('name'),
            'username' => $username,
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'role' => 'penjual',
            'status' => 'aktif',
        ]);

        if ($request->filled('stall_id')) {
            $stall = Stall::findOrFail($request->input('stall_id'));
            $stall->user_id = $user->id;
            $stall->save();
        } elseif ($request->filled('new_stall_name')) {
            $stallImage = 'assets/figma/f9dcf.png';
            if ($request->hasFile('new_stall_image')) {
                $path = $request->file('new_stall_image')->store('stalls', 'public');
                $stallImage = 'storage/'.$path;
            }

            Stall::create([
                'user_id' => $user->id,
                'name' => $request->input('new_stall_name'),
                'code' => $request->input('new_stall_code') ?: 'S-'.rand(10, 99),
                'location' => $request->input('new_stall_location', 'Kantin Tel-U'),
                'description' => $request->input('new_stall_description'),
                'opening_hours' => $request->input('new_stall_opening_hours', '08:00 - 17:00 WIB'),
                'contact_phone' => $request->input('phone'),
                'bank_info' => $request->input('new_stall_bank_info'),
                'is_open' => true,
                'rating' => 4.9,
                'estimated_time' => '10-15 mnt',
                'image' => $stallImage,
            ]);
        }

        return back()->with('success', "Akun penjual {$user->name} beserta data stan berhasil didaftarkan.");
    }

    public function resetPassword(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return back()->with('success', "Kata sandi untuk akun {$user->name} ({$user->email}) berhasil diperbarui.");
    }

    public function approveSellerRequest(Request $request, int $id): RedirectResponse
    {
        $sellerReq = SellerRequest::findOrFail($id);

        if ($sellerReq->status !== 'menunggu') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        // Check if user already exists or create new
        $user = User::where('email', $sellerReq->email)->first();

        if (! $user) {
            $username = explode('@', $sellerReq->email)[0];
            $base = $username;
            $i = 1;
            while (User::where('username', $username)->exists()) {
                $username = $base.$i++;
            }

            $user = User::create([
                'name' => $sellerReq->applicant_name,
                'username' => $username,
                'email' => $sellerReq->email,
                'phone' => $sellerReq->phone,
                'password' => Hash::make('password123'), // Default temporary password
                'role' => 'penjual',
                'status' => 'aktif',
            ]);
        } else {
            $user->role = 'penjual';
            $user->status = 'aktif';
            $user->save();
        }

        // Create or update stall for this seller
        $code = $sellerReq->stall_code ?: 'S-'.rand(10, 99);
        $stall = Stall::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $sellerReq->stall_name,
                'code' => $code,
                'location' => $sellerReq->stall_location,
                'description' => $sellerReq->stall_description,
                'opening_hours' => $sellerReq->opening_hours ?? '08:00 - 17:00 WIB',
                'contact_phone' => $sellerReq->contact_phone ?? $sellerReq->phone,
                'bank_info' => $sellerReq->bank_info,
                'is_open' => true,
                'rating' => 4.9,
                'estimated_time' => '10-15 mnt',
                'image' => 'assets/figma/f9dcf.png',
            ]
        );

        $sellerReq->update([
            'status' => 'disetujui',
            'admin_notes' => $request->input('admin_notes', 'Disetujui oleh Administrator.'),
            'user_id' => $user->id,
        ]);

        return back()->with('success', "Permintaan disetujui! Akun penjual {$user->name} ({$user->email}) dan Stan {$stall->name} telah aktif.");
    }

    public function rejectSellerRequest(Request $request, int $id): RedirectResponse
    {
        $sellerReq = SellerRequest::findOrFail($id);

        $sellerReq->update([
            'status' => 'ditolak',
            'admin_notes' => $request->input('admin_notes', 'Maaf, pendaftaran stan belum memenuhi persyaratan.'),
        ]);

        return back()->with('success', "Permintaan pendaftaran dari {$sellerReq->applicant_name} telah ditolak.");
    }

    public function toggleUserStatus(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat mengubah status akun Anda sendiri.');
        }

        $user->status = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->save();

        return back()->with('success', "Status akun {$user->name} berhasil diubah menjadi ".strtoupper($user->status));
    }

    public function updateUser(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:6'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'avatar' => ['nullable', 'image', 'max:5120'],
        ]);

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'status' => $request->input('status'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && str_starts_with($user->avatar, 'storage/')) {
                $oldPath = str_replace('storage/', '', $user->avatar);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = 'storage/'.$path;
        }

        $user->update($data);

        return back()->with('success', "Data akun {$user->name} berhasil diperbarui.");
    }

    public function deleteUser(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        // Check if seller has active orders (SRS 5c)
        if ($user->role === 'penjual' && $user->stall) {
            $activeOrdersCount = Order::where('stall_id', $user->stall->id)
                ->whereIn('status', ['menunggu', 'diproses', 'siap_diambil'])
                ->count();

            if ($activeOrdersCount > 0) {
                return back()->with('error', "Gagal menghapus: Penjual {$user->name} masih memiliki {$activeOrdersCount} pesanan aktif yang sedang berlangsung.");
            }
        }

        if ($user->avatar && str_starts_with($user->avatar, 'storage/')) {
            $oldPath = str_replace('storage/', '', $user->avatar);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $user->delete();

        return back()->with('success', 'Akun berhasil dihapus dari sistem.');
    }

    public function monitorTransactions(): View
    {
        $activeOrders = Order::with(['user', 'stall', 'items.menu'])
            ->whereIn('status', ['menunggu', 'diproses', 'siap_diambil'])
            ->latest()
            ->get();

        $allOrders = Order::with(['user', 'stall', 'items.menu'])
            ->latest()
            ->paginate(20);

        return view('admin.transactions', compact('activeOrders', 'allOrders'));
    }

    public function stallsAndCategories(): View
    {
        $stalls = Stall::with(['user'])->withCount('menus')->get();
        $categories = Category::withCount('menus')->get();
        $sellersWithoutStalls = User::where('role', 'penjual')->doesntHave('stall')->get();

        return view('admin.stalls_categories', compact('stalls', 'categories', 'sellersWithoutStalls'));
    }

    public function storeStall(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:stalls,name'],
            'location' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:10'],
            'user_id' => ['nullable', 'exists:users,id'],
            'image' => ['nullable', 'image', 'max:5120'],
        ], [
            'name.required' => 'Nama stan wajib diisi.',
            'name.unique' => 'Nama stan sudah ada.',
            'location.required' => 'Lokasi stan wajib diisi.',
        ]);

        $image = 'assets/figma/6a55e.png';
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('stalls', 'public');
            $image = 'storage/'.$path;
        }

        Stall::create([
            'name' => $request->input('name'),
            'location' => $request->input('location'),
            'code' => $request->input('code') ?: 'S-'.rand(10, 99),
            'user_id' => $request->input('user_id'),
            'is_open' => true,
            'image' => $image,
        ]);

        return back()->with('success', 'Stan baru berhasil ditambahkan.');
    }

    public function updateStall(Request $request, int $id): RedirectResponse
    {
        $stall = Stall::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:stalls,name,'.$stall->id],
            'location' => ['required', 'string', 'max:255'],
            'user_id' => ['nullable', 'exists:users,id'],
            'is_open' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $data = [
            'name' => $request->input('name'),
            'location' => $request->input('location'),
            'user_id' => $request->input('user_id'),
            'is_open' => $request->boolean('is_open'),
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

        return back()->with('success', 'Data stan berhasil diperbarui.');
    }

    public function deleteStall(int $id): RedirectResponse
    {
        $stall = Stall::with('menus')->findOrFail($id);

        $activeOrders = Order::where('stall_id', $stall->id)->whereIn('status', ['menunggu', 'diproses', 'siap_diambil'])->count();
        if ($activeOrders > 0) {
            return back()->with('error', "Stan tidak dapat dihapus karena masih memiliki {$activeOrders} pesanan aktif.");
        }

        if ($stall->image && str_starts_with($stall->image, 'storage/')) {
            $oldPath = str_replace('storage/', '', $stall->image);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        foreach ($stall->menus as $menu) {
            if ($menu->photo && str_starts_with($menu->photo, 'storage/')) {
                $mPath = str_replace('storage/', '', $menu->photo);
                if (Storage::disk('public')->exists($mPath)) {
                    Storage::disk('public')->delete($mPath);
                }
            }
        }

        $stall->delete();

        return back()->with('success', 'Stan berhasil dihapus.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'icon' => ['nullable', 'string', 'max:20'],
        ]);

        $slug = Str::slug($request->input('name'));

        Category::create([
            'name' => $request->input('name'),
            'slug' => $slug,
            'icon' => $request->input('icon', '🍽️'),
        ]);

        return back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,'.$category->id],
            'icon' => ['nullable', 'string', 'max:20'],
        ]);

        $category->update([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'icon' => $request->input('icon', $category->icon),
        ]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function deleteCategory(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    public function salesReport(Request $request): View|StreamedResponse
    {
        $period = $request->input('period', 'day'); // 'day', 'week', 'month'
        $startDate = match ($period) {
            'week' => Carbon::now()->subDays(7)->startOfDay(),
            'month' => Carbon::now()->subDays(30)->startOfDay(),
            default => Carbon::today()->startOfDay(),
        };

        $endDate = Carbon::now()->endOfDay();

        $stallsSummary = Stall::with(['orders' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate])
                ->where('payment_status', 'lunas')
                ->whereIn('status', ['diproses', 'siap_diambil', 'selesai']);
        }])->get()->map(function ($stall) {
            $orders = $stall->orders;
            $count = $orders->count();
            $revenue = $orders->sum('total_price');

            return (object) [
                'id' => $stall->id,
                'name' => $stall->name,
                'location' => $stall->location,
                'orders_count' => $count,
                'total_revenue' => $revenue,
            ];
        });

        $totalRevenueAll = $stallsSummary->sum('total_revenue');
        $totalOrdersAll = $stallsSummary->sum('orders_count');

        if (in_array($request->input('export'), ['excel', 'csv'])) {
            $fileName = 'laporan-penjualan-'.$period.'-'.Carbon::now()->format('Ymd_His').'.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ];

            return response()->stream(function () use ($stallsSummary, $totalRevenueAll, $totalOrdersAll, $period) {
                $handle = fopen('php://output', 'w');
                // Output UTF-8 BOM for Microsoft Excel compatibility
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                fputcsv($handle, ['LAPORAN PENJUALAN KANTIN TELKOM UNIVERSITY (KONSOLIDASI)']);
                fputcsv($handle, ['Periode:', strtoupper($period), 'Tanggal Cetak:', Carbon::now()->toDateTimeString()]);
                fputcsv($handle, []);
                fputcsv($handle, ['No', 'Nama Stan', 'Lokasi', 'Jumlah Pesanan Berhasil', 'Total Omzet (IDR)']);

                foreach ($stallsSummary as $index => $row) {
                    fputcsv($handle, [
                        $index + 1,
                        $row->name,
                        $row->location,
                        $row->orders_count,
                        $row->total_revenue,
                    ]);
                }

                fputcsv($handle, []);
                fputcsv($handle, ['', 'TOTAL KESELURUHAN', '', $totalOrdersAll, $totalRevenueAll]);
                fclose($handle);
            }, 200, $headers);
        }

        return view('admin.reports', compact('stallsSummary', 'period', 'totalRevenueAll', 'totalOrdersAll'));
    }
}
