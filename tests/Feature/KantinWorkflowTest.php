<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\SellerRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KantinWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Selamat Datang Kembali!');
        $response->assertSee('Masuk ke Akun');
        $response->assertSee('Kata Sandi');
    }

    public function test_buyer_login_with_email_redirects_to_menu(): void
    {
        $response = $this->post('/login', [
            'identifier' => 'farhan@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('menu.index'));
        $this->assertAuthenticated();
    }

    public function test_buyer_login_with_phone_or_username(): void
    {
        // Login by phone
        $response = $this->post('/login', [
            'identifier' => '08123456789',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('menu.index'));

        $this->post('/logout');

        // Login by username
        $response = $this->post('/login', [
            'identifier' => 'farhan',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('menu.index'));
    }

    public function test_seller_login_redirects_to_merchant_dashboard(): void
    {
        $response = $this->post('/login', [
            'identifier' => 'siti.aminah@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('merchant.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $response = $this->post('/login', [
            'identifier' => 'admin@telkomuniversity.ac.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_login_with_invalid_credentials_fails(): void
    {
        $response = $this->post('/login', [
            'identifier' => 'farhan@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('identifier');
        $this->assertGuest();
    }

    public function test_role_restriction_returns_access_denied_for_unauthorized_user(): void
    {
        $buyer = User::where('role', 'pembeli')->first();

        // Buyer attempting to access merchant dashboard
        $response = $this->actingAs($buyer)->get('/merchant');
        $response->assertStatus(403);
        $response->assertSee('Akses Ditolak');

        // Buyer attempting to access admin dashboard
        $response = $this->actingAs($buyer)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_new_buyer_registration(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Pratama',
            'email' => 'budi.pratama@gmail.com',
            'phone' => '081399881122',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'email' => 'budi.pratama@gmail.com',
            'role' => 'pembeli',
            'status' => 'aktif',
        ]);
    }

    public function test_buyer_can_add_to_cart_and_checkout(): void
    {
        $buyer = User::where('role', 'pembeli')->first();
        $menu = Menu::where('is_available', true)->where('stock', '>', 5)->first();

        $response = $this->actingAs($buyer)->post('/cart/add', [
            'menu_id' => $menu->id,
            'quantity' => 2,
            'spice_level' => 'Level 3 Sedang',
            'portion' => 'Porsi Reguler',
            'notes' => 'Tolong kuah dipisah',
        ]);

        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $buyer->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
        ]);

        // Proceed to checkout
        $response = $this->actingAs($buyer)->post('/checkout');
        $order = Order::where('user_id', $buyer->id)->latest('id')->first();
        $response->assertRedirect(route('orders.qris', $order->id));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $buyer->id,
            'status' => 'menunggu',
            'payment_status' => 'belum_bayar',
        ]);
    }

    public function test_public_scan_payment_via_google_lens_marks_order_as_paid_and_generates_queue_number(): void
    {
        $buyer = User::where('role', 'pembeli')->first();
        $menu = Menu::where('is_available', true)->where('stock', '>', 5)->first();
        $initialStock = $menu->stock;

        // Create pending order with order items
        $this->actingAs($buyer)->post('/cart/add', [
            'menu_id' => $menu->id,
            'quantity' => 2,
        ]);
        $this->actingAs($buyer)->post('/checkout');
        $order = Order::where('user_id', $buyer->id)->latest('id')->first();

        $this->assertEquals('belum_bayar', $order->payment_status);
        $this->assertNull($order->queue_number);

        // Simulate scanning QR code from mobile phone via Google Lens (unauthenticated guest scanner)
        $this->post('/logout');

        $cleanCode = str_replace('#', '', $order->order_code);
        $response = $this->get('/pay/'.$cleanCode);

        $response->assertStatus(200);
        $response->assertSee('Pembayaran QRIS Berhasil');

        $order->refresh();
        $this->assertEquals('lunas', $order->payment_status);
        $this->assertEquals('diproses', $order->status);
        $this->assertNotNull($order->queue_number);
        $this->assertStringContainsString('A-', $order->queue_number);

        // Verify stock deducted
        $menu->refresh();
        $this->assertEquals($initialStock - 2, $menu->stock);

        // Verify order status API returns is_paid = true
        $apiResponse = $this->getJson("/api/orders/{$order->id}/status");
        $apiResponse->assertStatus(200);
        $apiResponse->assertJson([
            'is_paid' => true,
            'payment_status' => 'lunas',
            'status' => 'diproses',
        ]);
    }

    public function test_seller_can_confirm_payment_and_generate_queue_number(): void
    {
        $seller = User::where('role', 'penjual')->first();
        $stall = $seller->stall;
        $buyer = User::where('role', 'pembeli')->first();

        // Create pending order
        $order = Order::create([
            'order_code' => '#TEST-999',
            'user_id' => $buyer->id,
            'stall_id' => $stall->id,
            'queue_number' => null,
            'order_date' => now()->toDateString(),
            'status' => 'menunggu',
            'payment_status' => 'lunas',
            'subtotal' => 15000,
            'service_fee' => 1000,
            'discount' => 0,
            'total_price' => 16000,
        ]);

        $response = $this->actingAs($seller)->post(route('merchant.orders.confirm', $order->id));
        $response->assertRedirect();

        $order->refresh();
        $this->assertEquals('diproses', $order->status);
        $this->assertNotNull($order->queue_number);
        $this->assertStringContainsString('A-', $order->queue_number);
    }

    public function test_seller_can_advance_order_status_step_by_step(): void
    {
        $seller = User::where('role', 'penjual')->first();
        $stall = $seller->stall;
        $buyer = User::where('role', 'pembeli')->first();

        $order = Order::create([
            'order_code' => '#TEST-1000',
            'user_id' => $buyer->id,
            'stall_id' => $stall->id,
            'queue_number' => 'A-099',
            'order_date' => now()->toDateString(),
            'status' => 'diproses',
            'payment_status' => 'lunas',
            'subtotal' => 20000,
            'total_price' => 21000,
        ]);

        // Step 1: Advance to siap_diambil
        $response = $this->actingAs($seller)->post(route('merchant.orders.status', $order->id), [
            'status' => 'siap_diambil',
        ]);
        $response->assertRedirect();
        $order->refresh();
        $this->assertEquals('siap_diambil', $order->status);

        // Verify notification created for buyer
        $this->assertDatabaseHas('notifications', [
            'user_id' => $buyer->id,
            'order_id' => $order->id,
        ]);

        // Step 2: Advance to selesai
        $response = $this->actingAs($seller)->post(route('merchant.orders.status', $order->id), [
            'status' => 'selesai',
        ]);
        $response->assertRedirect();
        $order->refresh();
        $this->assertEquals('selesai', $order->status);
    }

    public function test_admin_can_reset_password_for_any_user(): void
    {
        $admin = User::where('role', 'admin')->first();
        $buyer = User::where('role', 'pembeli')->first();

        $response = $this->actingAs($admin)->post(route('admin.users.reset-password', $buyer->id), [
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $buyer->refresh();
        $this->assertTrue(Hash::check('newpassword123', $buyer->password));
    }

    public function test_admin_can_create_seller_with_full_stall_biodata(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post(route('admin.sellers.store'), [
            'name' => 'Pak Joko Sate',
            'email' => 'joko.sate@gmail.com',
            'phone' => '081299998888',
            'password' => 'password123',
            'new_stall_name' => 'Sate Madura Pak Joko',
            'new_stall_code' => 'B-09',
            'new_stall_location' => 'Kantin Terpadu Selaru Lt. 1',
            'new_stall_opening_hours' => '09:00 - 18:00 WIB',
            'new_stall_description' => 'Sate ayam dan kambing bumbu kacang gurih manis.',
            'new_stall_bank_info' => 'BCA 12345678 a.n Joko',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'joko.sate@gmail.com',
            'role' => 'penjual',
        ]);
        $this->assertDatabaseHas('stalls', [
            'name' => 'Sate Madura Pak Joko',
            'code' => 'B-09',
            'location' => 'Kantin Terpadu Selaru Lt. 1',
            'opening_hours' => '09:00 - 18:00 WIB',
        ]);
    }

    public function test_buyer_can_submit_seller_request_and_admin_can_approve_it(): void
    {
        $buyer = User::where('role', 'pembeli')->first();
        $admin = User::where('role', 'admin')->first();

        // 1. Submit request
        $response = $this->actingAs($buyer)->post(route('seller.register.submit'), [
            'applicant_name' => 'Ahmad Rabbani',
            'email' => 'ahmad.rabbani@gmail.com',
            'phone' => '087812345678',
            'stall_name' => 'Kebab Turki Tel-U',
            'stall_location' => 'Kantin GKU Lt. 1',
            'stall_description' => 'Kebab daging sapi dan keju mozarella.',
            'opening_hours' => '10:00 - 19:00 WIB',
            'contact_phone' => '087812345678',
            'bank_info' => 'BNI 8899112233',
        ]);

        $response->assertRedirect(route('seller.register.form'));
        $this->assertDatabaseHas('seller_requests', [
            'email' => 'ahmad.rabbani@gmail.com',
            'stall_name' => 'Kebab Turki Tel-U',
            'status' => 'menunggu',
        ]);

        $sellerReq = SellerRequest::where('email', 'ahmad.rabbani@gmail.com')->first();

        // 2. Admin approves request
        $approveResponse = $this->actingAs($admin)->post(route('admin.seller-requests.approve', $sellerReq->id));
        $approveResponse->assertRedirect();

        $sellerReq->refresh();
        $this->assertEquals('disetujui', $sellerReq->status);

        $this->assertDatabaseHas('users', [
            'email' => 'ahmad.rabbani@gmail.com',
            'role' => 'penjual',
        ]);
        $this->assertDatabaseHas('stalls', [
            'name' => 'Kebab Turki Tel-U',
            'location' => 'Kantin GKU Lt. 1',
        ]);
    }

    public function test_seller_can_update_stall_profile_biodata(): void
    {
        $seller = User::where('role', 'penjual')->first();
        $stall = $seller->stall;

        $response = $this->actingAs($seller)->post(route('merchant.stall.update'), [
            'name' => 'Warung Bu Siti Spesial Pedas',
            'code' => 'A-01',
            'location' => 'Lantai 1, Blok A Kantin GKU Tel-U',
            'opening_hours' => '07:00 - 18:00 WIB',
            'contact_phone' => '081233445511',
            'bank_info' => 'BCA 82719281 a.n Siti Aminah',
            'description' => 'Menu ayam geprek sambal bawang dan aneka masakan rumahan.',
            'is_open' => '1',
        ]);

        $response->assertRedirect();
        $stall->refresh();
        $this->assertEquals('Warung Bu Siti Spesial Pedas', $stall->name);
        $this->assertEquals('07:00 - 18:00 WIB', $stall->opening_hours);
    }

    public function test_merchant_can_export_reports_to_excel_csv(): void
    {
        $seller = User::where('role', 'penjual')->first();

        $response = $this->actingAs($seller)->get(route('merchant.reports', ['export' => 'excel', 'period' => 'today']));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename="Laporan-Keuangan-', $response->headers->get('Content-Disposition'));
    }

    public function test_admin_can_export_sales_reports_to_excel_csv(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.reports', ['export' => 'excel', 'period' => 'day']));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename="laporan-penjualan-', $response->headers->get('Content-Disposition'));
    }

    public function test_user_can_view_profile_biodata_and_expenses(): void
    {
        $buyer = User::where('role', 'pembeli')->first();

        $response = $this->actingAs($buyer)->get(route('profile.index'));
        $response->assertStatus(200);
        $response->assertSee($buyer->name);
        $response->assertSee($buyer->email);
        $response->assertSee('TOTAL PENGELUARAN');
        $response->assertSee('Riwayat Pesanan');
        $response->assertSee('Rincian Stan');
        $response->assertSee('Edit Biodata Akun');
    }

    public function test_user_can_update_biodata_profile(): void
    {
        $buyer = User::where('role', 'pembeli')->first();

        $response = $this->actingAs($buyer)->post(route('profile.update'), [
            'name' => 'Farhan Updated Alamsyah',
            'phone' => '089911223344',
        ]);

        $response->assertRedirect();
        $buyer->refresh();
        $this->assertEquals('Farhan Updated Alamsyah', $buyer->name);
        $this->assertEquals('089911223344', $buyer->phone);
    }

    public function test_quick_login_bypass_is_disabled(): void
    {
        $response = $this->get('/quick-login/admin');
        $response->assertStatus(404);
    }

    public function test_merchant_can_complete_all_ready_orders_at_once(): void
    {
        $seller = User::where('role', 'penjual')->first();
        $stall = $seller->stall;
        $buyer = User::where('role', 'pembeli')->first();

        $order1 = Order::create([
            'order_code' => '#TEST-READY-1',
            'user_id' => $buyer->id,
            'stall_id' => $stall->id,
            'queue_number' => 'A-101',
            'order_date' => now()->toDateString(),
            'status' => 'siap_diambil',
            'payment_status' => 'lunas',
            'subtotal' => 15000,
            'total_price' => 16000,
        ]);

        $order2 = Order::create([
            'order_code' => '#TEST-READY-2',
            'user_id' => $buyer->id,
            'stall_id' => $stall->id,
            'queue_number' => 'A-102',
            'order_date' => now()->toDateString(),
            'status' => 'siap_diambil',
            'payment_status' => 'lunas',
            'subtotal' => 18000,
            'total_price' => 19000,
        ]);

        $response = $this->actingAs($seller)->post(route('merchant.orders.complete-all'));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order1->refresh();
        $order2->refresh();

        $this->assertEquals('selesai', $order1->status);
        $this->assertEquals('selesai', $order2->status);
    }

    public function test_food_has_spice_levels_while_drink_has_no_spice(): void
    {
        $seller = User::where('role', 'penjual')->first();
        $foodCategory = Category::where('slug', 'makanan')->first();
        $drinkCategory = Category::where('slug', 'minuman')->first();

        // 1. Create Food menu with spice levels enabled
        $responseFood = $this->actingAs($seller)->post(route('merchant.menus.store'), [
            'name' => 'Ayam Penyet Pedas',
            'category_id' => $foodCategory->id,
            'price' => 20000,
            'stock' => 15,
            'prep_time' => '10 mnt',
            'has_spice_levels' => 1,
            'has_jumbo_portion' => 1,
        ]);
        $responseFood->assertRedirect();

        $foodMenu = Menu::where('name', 'Ayam Penyet Pedas')->first();
        $this->assertNotNull($foodMenu);
        $this->assertIsArray($foodMenu->spice_levels);
        $this->assertContains('Level 1 Santai', $foodMenu->spice_levels);

        // 2. Create Drink menu (Minuman should NOT have spice levels even if checkbox was passed)
        $responseDrink = $this->actingAs($seller)->post(route('merchant.menus.store'), [
            'name' => 'Es Teh Kampul',
            'category_id' => $drinkCategory->id,
            'price' => 5000,
            'stock' => 50,
            'prep_time' => '3 mnt',
            'has_spice_levels' => 1,
        ]);
        $responseDrink->assertRedirect();

        $drinkMenu = Menu::where('name', 'Es Teh Kampul')->first();
        $this->assertNotNull($drinkMenu);
        $this->assertNull($drinkMenu->spice_levels);
    }

    public function test_merchant_dashboard_shows_payment_confirmation_and_complete_all(): void
    {
        $seller = User::where('role', 'penjual')->first();

        $response = $this->actingAs($seller)->get(route('merchant.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('1. MENUNGGU PEMBAYARAN');
        $response->assertSee('2. SEDANG DIMASAK');
        $response->assertSee('3. SIAP DI ETALASE');
    }

    public function test_onboarding_page_renders_mobile_hero_and_ctas(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Makan di Kantin');
        $response->assertSee('Tanpa Antre Panjang');
        $response->assertSee('Ambil di Tempat');
        $response->assertSee('Bayar Instan QRIS');
        $response->assertSee('Daftar Akun Baru');
    }

    public function test_buyer_catalog_renders_clean_mobile_header_and_popular_menu(): void
    {
        $response = $this->get(route('menu.index'));
        $response->assertStatus(200);
        $response->assertSee('AMBIL DI');
        $response->assertSee('Kantin Gedung B / FEB Telkom Univ');
        $response->assertSee('Stan Kantin Buka');
        $response->assertSee('Menu Populer Kampus');
        $response->assertDontSee('SPESIAL SIVITAS');
        $response->assertDontSee('Diskon Mahasiswa');
    }

    public function test_cart_and_checkout_has_no_discount(): void
    {
        $buyer = User::where('role', 'pembeli')->first();
        $menu = Menu::first();

        CartItem::where('user_id', $buyer->id)->delete();
        CartItem::create([
            'user_id' => $buyer->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
            'portion' => 'Porsi Reguler',
            'subtotal' => $menu->price * 2,
        ]);

        $response = $this->actingAs($buyer)->get(route('cart.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Diskon Mahasiswa');
        $response->assertDontSee('Hemat Rp 5.000');
    }

    public function test_menu_detail_page_renders_clean_mobile_layout(): void
    {
        $menu = Menu::first();

        $response = $this->get(route('menu.show', $menu->id));
        $response->assertStatus(200);
        $response->assertSee('Detail Stan');
        $response->assertSee($menu->name);
        $response->assertSee('Catatan Khusus Stan');
        $response->assertSee('Porsi Nasi / Menu');
    }

    public function test_cart_page_renders_compact_mobile_layout_with_qris_and_totals(): void
    {
        $buyer = User::where('role', 'pembeli')->first();
        $menu = Menu::first();

        CartItem::create([
            'user_id' => $buyer->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
            'portion' => 'Porsi Reguler',
            'subtotal' => $menu->price * 2,
        ]);

        $response = $this->actingAs($buyer)->get(route('cart.index'));
        $response->assertStatus(200);
        $response->assertSee('Checkout');
        $response->assertSee('Qris Tel U');
        $response->assertSee('Kantin Terpadu Tel-U');
        $response->assertSee('Metode Pembayaran');
    }

    public function test_storage_fallback_route_serves_image_or_placeholder(): void
    {
        Storage::disk('public')->put('menus/test_file.txt', 'test content');

        $response = $this->get('/storage/menus/test_file.txt');
        $response->assertStatus(200);

        Storage::disk('public')->delete('menus/test_file.txt');

        // Non-existent image returns 404 from storage route, which triggers onerror in frontend
        $notFoundResponse = $this->get('/storage/menus/non_existent_image_12345.png');
        $notFoundResponse->assertStatus(404);

        // Menu model photo_url accessor test
        $menu = Menu::first();
        $this->assertNotEmpty($menu->photo_url);

        // User avatar_url accessor test
        $buyer = User::where('role', 'pembeli')->first();
        $this->assertNotEmpty($buyer->avatar_url);
    }

    public function test_layout_includes_tailwind_cdn_and_safe_vite_loader(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('cdn.tailwindcss.com');
        $response->assertSee('telkom-red');
    }

    public function test_merchant_menus_page_renders_new_desktop_and_mobile_layout_and_can_clear_all_stock(): void
    {
        $seller = User::where('role', 'penjual')->first();

        $response = $this->actingAs($seller)->get(route('merchant.menus.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Menu');
        $response->assertSee('Habiskan Semua Stok Hari Ini');
        $response->assertSee('Total Menu Aktif');
        $response->assertSee('Menu Stok Kritis');

        // Test Clear all stock action
        $clearResponse = $this->actingAs($seller)->post(route('merchant.menus.clear-all-stock'));
        $clearResponse->assertRedirect();
        $this->assertEquals(0, Menu::where('stall_id', $seller->stall->id)->where('is_available', true)->count());
    }

    public function test_merchant_reports_page_renders_kpis_and_hourly_chart(): void
    {
        $seller = User::where('role', 'penjual')->first();

        $response = $this->actingAs($seller)->get(route('merchant.reports'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Penjualan');
        $response->assertSee('Tren Pesanan per Jam');
        $response->assertSee('Unduh Excel (.xlsx)');
        $response->assertSee('100% Non-Tunai');
    }

    public function test_merchant_stall_profile_updates_sla_and_operations_settings(): void
    {
        $seller = User::where('role', 'penjual')->first();

        $response = $this->actingAs($seller)->get(route('merchant.stall.profile'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Stan');
        $response->assertSee('SLA Dapur');
        $response->assertSee('Daftar Akun Kasir');

        $updateResponse = $this->actingAs($seller)->post(route('merchant.stall.update'), [
            'name' => 'Ayam Geprek Kampus Tel-U',
            'location' => 'Lantai 1, Blok A-03 Kantin TULT',
            'opening_hours' => 'Senin - Sabtu • 08:00 - 16:30 WIB',
            'contact_phone' => '0812-3456-7890',
            'culinary_category' => 'Makanan Berat & Olahan Ayam',
            'max_active_queues' => 20,
            'sla_minutes' => 7,
            'auto_accept_qris' => true,
            'sound_notification' => true,
            'bank_info' => '1310-0982-1234 Bank Mandiri',
            'description' => 'Ayam geprek krispi resep sambal korek khas Tel-U.',
            'is_open' => true,
        ]);

        $updateResponse->assertRedirect();
        $stall = $seller->stall->fresh();
        $this->assertEquals('Ayam Geprek Kampus Tel-U', $stall->name);
        $this->assertEquals(20, $stall->max_active_queues);
        $this->assertEquals(7, $stall->sla_minutes);
    }

    public function test_user_profile_avatar_and_biodata_update(): void
    {
        Storage::fake('public');

        $buyer = User::where('role', 'pembeli')->first();
        $avatarFile = UploadedFile::fake()->image('my-avatar.jpg', 200, 200);

        $response = $this->actingAs($buyer)->post(route('profile.update'), [
            'name' => 'Farhan Updated',
            'phone' => '089912345678',
            'username' => 'farhan_new',
            'avatar' => $avatarFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $buyer->refresh();
        $this->assertEquals('Farhan Updated', $buyer->name);
        $this->assertEquals('089912345678', $buyer->phone);
        $this->assertEquals('farhan_new', $buyer->username);
        $this->assertNotNull($buyer->avatar);
        $this->assertStringStartsWith('storage/avatars/', $buyer->avatar);

        $storedPath = str_replace('storage/', '', $buyer->avatar);
        Storage::disk('public')->assertExists($storedPath);
    }

    public function test_merchant_menu_image_lifecycle_and_cleanup(): void
    {
        Storage::fake('public');

        $seller = User::where('role', 'penjual')->first();
        $category = Category::first();
        $menuImage = UploadedFile::fake()->image('menu-ayam.jpg', 300, 300);

        // 1. Store menu with image
        $storeResponse = $this->actingAs($seller)->post(route('merchant.menus.store'), [
            'name' => 'Ayam Penyet Sambal Matah',
            'category_id' => $category->id,
            'price' => 18000,
            'stock' => 25,
            'photo' => $menuImage,
            'has_spice_levels' => 1,
        ]);

        $storeResponse->assertRedirect();
        $menu = Menu::where('name', 'Ayam Penyet Sambal Matah')->first();
        $this->assertNotNull($menu);
        $this->assertStringStartsWith('storage/menus/', $menu->photo);

        $initialPath = str_replace('storage/', '', $menu->photo);
        Storage::disk('public')->assertExists($initialPath);

        // 2. Update menu with new image and ensure old image is removed
        $newMenuImage = UploadedFile::fake()->image('menu-ayam-baru.jpg', 300, 300);
        $updateResponse = $this->actingAs($seller)->post(route('merchant.menus.update', $menu->id), [
            'name' => 'Ayam Penyet Sambal Matah Spesial',
            'category_id' => $category->id,
            'price' => 20000,
            'stock' => 30,
            'photo' => $newMenuImage,
        ]);

        $updateResponse->assertRedirect();
        $menu->refresh();
        $newPath = str_replace('storage/', '', $menu->photo);

        Storage::disk('public')->assertMissing($initialPath);
        Storage::disk('public')->assertExists($newPath);

        // 3. Delete menu and ensure image is cleaned up
        $deleteResponse = $this->actingAs($seller)->post(route('merchant.menus.delete', $menu->id));
        $deleteResponse->assertRedirect();

        $this->assertNull(Menu::find($menu->id));
        Storage::disk('public')->assertMissing($newPath);
    }

    public function test_logout_works_for_both_get_and_post(): void
    {
        $seller = User::where('role', 'penjual')->first();

        // Test POST logout
        $postLogout = $this->actingAs($seller)->post(route('logout'));
        $postLogout->assertRedirect(route('login'));
        $this->assertGuest();

        // Test GET logout
        $getLogout = $this->actingAs($seller)->get(route('logout'));
        $getLogout->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_seller_and_admin_navigating_cart_and_orders_does_not_throw_403(): void
    {
        $seller = User::where('role', 'penjual')->first();
        $admin = User::where('role', 'admin')->first();

        // Seller navigating to cart or orders redirects to merchant dashboard without 403
        $sellerCart = $this->actingAs($seller)->get(route('cart.index'));
        $sellerCart->assertRedirect(route('merchant.dashboard'));

        $sellerOrders = $this->actingAs($seller)->get(route('orders.index'));
        $sellerOrders->assertRedirect(route('merchant.dashboard'));

        // Admin navigating to cart or orders redirects to admin dashboard/transactions without 403
        $adminCart = $this->actingAs($admin)->get(route('cart.index'));
        $adminCart->assertRedirect(route('admin.dashboard'));

        $adminOrders = $this->actingAs($admin)->get(route('orders.index'));
        $adminOrders->assertRedirect(route('admin.transactions'));
    }

    public function test_admin_and_seller_can_update_profile_avatar_and_biodata(): void
    {
        Storage::fake('public');

        $admin = User::where('role', 'admin')->first();
        $avatarFile = UploadedFile::fake()->image('admin-avatar.jpg', 200, 200);

        $response = $this->actingAs($admin)->post(route('profile.update'), [
            'name' => 'Direktorat Logistik Tel-U Updated',
            'phone' => '081299887766',
            'username' => 'admin_updated',
            'avatar' => $avatarFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertEquals('Direktorat Logistik Tel-U Updated', $admin->name);
        $this->assertEquals('081299887766', $admin->phone);
        $this->assertEquals('admin_updated', $admin->username);
        $this->assertNotNull($admin->avatar);
        $this->assertStringStartsWith('storage/avatars/', $admin->avatar);
    }
}
