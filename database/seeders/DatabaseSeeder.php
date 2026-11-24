<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SellerRequest;
use App\Models\Stall;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@telkomuniversity.ac.id'],
            [
                'name' => 'Direktorat Logistik Tel-U',
                'username' => 'admin',
                'phone' => '081199887711',
                'role' => 'admin',
                'status' => 'aktif',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Sellers & Stalls
        $seller1 = User::firstOrCreate(
            ['email' => 'siti.aminah@gmail.com'],
            [
                'name' => 'Siti Aminah',
                'username' => 'siti_aminah',
                'phone' => '081233445511',
                'role' => 'penjual',
                'status' => 'aktif',
                'password' => Hash::make('password123'),
            ]
        );

        $stall1 = Stall::firstOrCreate(
            ['name' => 'Warung Bu Siti'],
            [
                'user_id' => $seller1->id,
                'code' => 'A-01',
                'location' => 'Lantai 1, Blok A Kantin GKU Tel-U',
                'description' => 'Menyajikan aneka masakan rumahan khas nusantara, ayam geprek sambal bawang pedas nampol, mie aceh tumis, dan aneka lauk pauk segar higienis setiap hari untuk civitas akademika Tel-U.',
                'opening_hours' => '07:30 - 17:00 WIB',
                'contact_phone' => '081233445511',
                'bank_info' => 'BCA 82719281 a.n Siti Aminah / Tel-U Pay',
                'is_open' => true,
                'rating' => 4.9,
                'estimated_time' => '10-15 mnt',
                'image' => 'assets/figma/f9dcf.png',
            ]
        );

        $seller2 = User::firstOrCreate(
            ['email' => 'pakjoko.mie@gmail.com'],
            [
                'name' => 'Joko Purwanto',
                'username' => 'joko_mie',
                'phone' => '081388221002',
                'role' => 'penjual',
                'status' => 'aktif',
                'password' => Hash::make('password123'),
            ]
        );

        $stall2 = Stall::firstOrCreate(
            ['name' => 'Mie Ayam Pak Joko'],
            [
                'user_id' => $seller2->id,
                'code' => 'A-02',
                'location' => 'Gedung GKU Lt. 1 A-12',
                'description' => 'Spesialis mie ayam pangsit buatan sendiri bertekstur kenyal dengan potongan ayam kecap gurih manis dan kuah kaldu sapi asli.',
                'opening_hours' => '08:00 - 16:30 WIB',
                'contact_phone' => '081388221002',
                'bank_info' => 'Mandiri 13000987123 a.n Joko Purwanto',
                'is_open' => true,
                'rating' => 4.8,
                'estimated_time' => '8-12 mnt',
                'image' => 'assets/figma/f99f9.png',
            ]
        );

        $seller3 = User::firstOrCreate(
            ['email' => 'budigeprek@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'username' => 'budi_geprek',
                'phone' => '081277889900',
                'role' => 'penjual',
                'status' => 'aktif',
                'password' => Hash::make('password123'),
            ]
        );

        $stall3 = Stall::firstOrCreate(
            ['name' => 'Ayam Geprek Mas Budi'],
            [
                'user_id' => $seller3->id,
                'code' => 'A-03',
                'location' => 'GKU Lt. 1 A-08',
                'description' => 'Ayam geprek krispi renyah dengan cabai pilihan level 1-10, lalapan komplit, tahu tempe goreng, dan sambal matah / bawang.',
                'opening_hours' => '08:30 - 17:30 WIB',
                'contact_phone' => '081277889900',
                'bank_info' => 'BRI 020901002341 a.n Budi Santoso',
                'is_open' => true,
                'rating' => 4.9,
                'estimated_time' => '10-15 mnt',
                'image' => 'assets/figma/6a55e.png',
            ]
        );

        $seller4 = User::firstOrCreate(
            ['email' => 'esteler.juara@telkom.id'],
            [
                'name' => 'Supriadi Hadi',
                'username' => 'kopi_telu',
                'phone' => '081299887766',
                'role' => 'penjual',
                'status' => 'aktif',
                'password' => Hash::make('password123'),
            ]
        );

        $stall4 = Stall::firstOrCreate(
            ['name' => 'Kopi Tel-U Corner'],
            [
                'user_id' => $seller4->id,
                'code' => 'B-04',
                'location' => 'Kantin Terpadu Gedung Selaru Lt. 1',
                'description' => 'Kedai kopi dan minuman segar favorit mahasiswa Tel-U. Menyajikan aneka racikan espresso robusta lokal, es kopi aren, es jeruk peras, dan teh melati jumbo.',
                'opening_hours' => '07:00 - 18:00 WIB',
                'contact_phone' => '081299887766',
                'bank_info' => 'BCA 541098124 a.n Supriadi Hadi',
                'is_open' => true,
                'rating' => 4.9,
                'estimated_time' => '3-5 mnt',
                'image' => 'assets/figma/8df5b.png',
            ]
        );

        // 3. Buyers
        $buyer1 = User::firstOrCreate(
            ['email' => 'farhan@gmail.com'],
            [
                'name' => 'Farhan Alamsyah',
                'username' => 'farhan',
                'phone' => '08123456789',
                'role' => 'pembeli',
                'status' => 'aktif',
                'password' => Hash::make('password123'),
            ]
        );

        $buyer2 = User::firstOrCreate(
            ['email' => 'dimas@gmail.com'],
            [
                'name' => 'Dimas Arya Pratama',
                'username' => 'dimas_arya',
                'phone' => '081299881122',
                'role' => 'pembeli',
                'status' => 'aktif',
                'password' => Hash::make('password123'),
            ]
        );

        $buyer3 = User::firstOrCreate(
            ['email' => 'daffa@student.telkomuniversity.ac.id'],
            [
                'name' => 'Daffa Naufal',
                'username' => 'daffa_naufal',
                'phone' => '082164332211',
                'role' => 'pembeli',
                'status' => 'aktif',
                'password' => Hash::make('password123'),
            ]
        );

        // 4. Categories
        $catMakanan = Category::firstOrCreate(['slug' => 'makanan'], ['name' => 'Makanan', 'icon' => '🍜']);
        $catMie = Category::firstOrCreate(['slug' => 'mie-bakso'], ['name' => 'Mie & Bakso', 'icon' => '🍲']);
        $catMinuman = Category::firstOrCreate(['slug' => 'minuman'], ['name' => 'Minuman', 'icon' => '🥤']);
        $catCamilan = Category::firstOrCreate(['slug' => 'camilan'], ['name' => 'Camilan', 'icon' => '🥐']);

        // 5. Menus
        // Warung Bu Siti
        $menuGeprek = Menu::firstOrCreate(
            ['name' => 'Nasi Ayam Geprek Sambal Bawang Spesial Tel-U', 'stall_id' => $stall1->id],
            [
                'category_id' => $catMakanan->id,
                'description' => 'Ayam goreng krispi berbalut sambal bawang ulek segar khas Bu Siti, disajikan hangat dengan nasi pulen Tel-U, lalapan timun segar, dan tahu goreng gurih.',
                'price' => 15000,
                'original_price' => 18000,
                'photo' => 'assets/figma/6a55e.png',
                'stock' => 18,
                'is_available' => true,
                'prep_time' => '8-12 mnt',
                'spice_levels' => ['Level 1 Santai', 'Level 3 Sedang', 'Level 5 Nampol 🔥'],
                'portion_options' => [
                    ['name' => 'Porsi Reguler (Standar)', 'price' => 0],
                    ['name' => 'Porsi Jumbo (Kenyang Maksimal)', 'price' => 3000],
                ],
            ]
        );

        $menuMieAceh = Menu::firstOrCreate(
            ['name' => 'Mie Aceh Daging Tumis (Basah / Nyemek)', 'stall_id' => $stall1->id],
            [
                'category_id' => $catMakanan->id,
                'description' => 'Mie kuning tebal khas Aceh dengan irisan daging sapi empuk, bumbu rempah aromatik, tauge, dan kuah kental gurih.',
                'price' => 22000,
                'original_price' => null,
                'photo' => 'assets/figma/28b3b.png',
                'stock' => 12,
                'is_available' => true,
                'prep_time' => '10-15 mnt',
                'spice_levels' => ['Level 1 Sedang', 'Level 2 Pedas'],
                'portion_options' => null,
            ]
        );

        $menuSambal = Menu::firstOrCreate(
            ['name' => 'Sambal Bawang Ulek', 'stall_id' => $stall1->id],
            [
                'category_id' => $catMakanan->id,
                'description' => 'Ekstra porsi sambal bawang segar ulekan tangan.',
                'price' => 3000,
                'original_price' => null,
                'photo' => 'assets/figma/c544a.png',
                'stock' => 3,
                'is_available' => true,
                'prep_time' => '2 mnt',
                'spice_levels' => null,
                'portion_options' => null,
            ]
        );

        $menuPisang = Menu::firstOrCreate(
            ['name' => 'Pisang Goreng Keju C', 'stall_id' => $stall1->id],
            [
                'category_id' => $catCamilan->id,
                'description' => 'Pisang goreng manis krispi bertabur parutan keju gurih dan susu kental manis.',
                'price' => 8000,
                'original_price' => null,
                'photo' => 'assets/figma/0db85.png',
                'stock' => 0, // Habis untuk test visual penanda habis
                'is_available' => false,
                'prep_time' => '5 mnt',
                'spice_levels' => null,
                'portion_options' => null,
            ]
        );

        $menuTeh = Menu::firstOrCreate(
            ['name' => 'Es Teh Manis Jumbo', 'stall_id' => $stall1->id],
            [
                'category_id' => $catMinuman->id,
                'description' => 'Teh melati wangi dingin segar ukuran jumbo.',
                'price' => 4000,
                'original_price' => null,
                'photo' => 'assets/figma/f4478.png',
                'stock' => 50,
                'is_available' => true,
                'prep_time' => '2 mnt',
                'spice_levels' => null,
                'portion_options' => null,
            ]
        );

        $menuJeruk = Menu::firstOrCreate(
            ['name' => 'Es Jeruk Peras Murni', 'stall_id' => $stall1->id],
            [
                'category_id' => $catMinuman->id,
                'description' => 'Jeruk peras murni gula asli 100%, dingin dan menyegarkan.',
                'price' => 6000,
                'original_price' => null,
                'photo' => 'assets/figma/47443.png',
                'stock' => 20,
                'is_available' => true,
                'prep_time' => '3 mnt',
                'spice_levels' => null,
                'portion_options' => null,
            ]
        );

        // Mie Ayam Pak Joko
        Menu::firstOrCreate(
            ['name' => 'Mie Ayam Pangsit Komplit', 'stall_id' => $stall2->id],
            [
                'category_id' => $catMie->id,
                'description' => 'Mie kenyal gurih, ayam kecap lembut bumbu rempah, pangsit basah dan pangsit krispi.',
                'price' => 14000,
                'original_price' => null,
                'photo' => 'assets/figma/f99f9.png',
                'stock' => 25,
                'is_available' => true,
                'prep_time' => '8 mnt',
                'spice_levels' => null,
                'portion_options' => null,
            ]
        );

        // Kopi Tel-U Corner
        Menu::firstOrCreate(
            ['name' => 'Es Kopi Susu Aren Tel-U', 'stall_id' => $stall4->id],
            [
                'category_id' => $catMinuman->id,
                'description' => 'Espresso robusta lokal, susu creamy segar, dan sirup gula aren asli Tasikmalaya.',
                'price' => 10000,
                'original_price' => null,
                'photo' => 'assets/figma/8df5b.png',
                'stock' => 30,
                'is_available' => true,
                'prep_time' => '3 mnt',
                'spice_levels' => null,
                'portion_options' => null,
            ]
        );

        // 6. Pre-seed Active Orders matching the Figma mockups!
        // Order 1 for Farhan: Active Order in Progress (A-012)
        $order1 = Order::firstOrCreate(
            ['order_code' => '#TELU-8924'],
            [
                'user_id' => $buyer1->id,
                'stall_id' => $stall1->id,
                'queue_number' => 'A-012',
                'order_date' => Carbon::now()->toDateString(),
                'status' => 'diproses',
                'payment_status' => 'lunas',
                'payment_method' => 'qris_telu_pay',
                'payment_time' => Carbon::now()->subMinutes(6),
                'subtotal' => 31000,
                'service_fee' => 1000,
                'discount' => 5000,
                'total_price' => 27000,
                'notes' => 'Level 3 Sedang, sambal dipisah ya bu',
                'estimated_ready_time' => 'Perkiraan siap dalam ~6 Menit',
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order1->id, 'menu_name' => 'Nasi Ayam Geprek Sambal Bawang Spesial Tel-U'],
            [
                'menu_id' => $menuGeprek->id,
                'quantity' => 1,
                'unit_price' => 15000,
                'subtotal' => 15000,
                'spice_level' => 'Level 3 Sedang',
                'portion' => 'Porsi Reguler',
                'notes' => 'Sambal dipisah',
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order1->id, 'menu_name' => 'Es Teh Manis Jumbo'],
            [
                'menu_id' => $menuTeh->id,
                'quantity' => 2,
                'unit_price' => 4000,
                'subtotal' => 8000,
                'spice_level' => null,
                'portion' => null,
                'notes' => 'Kurang manis, es banyak',
            ]
        );

        // Order 2 for Dimas: Waiting Seller Approval (Menunggu Konfirmasi)
        $order2 = Order::firstOrCreate(
            ['order_code' => '#TU-240501'],
            [
                'user_id' => $buyer2->id,
                'stall_id' => $stall1->id,
                'queue_number' => null,
                'order_date' => Carbon::now()->toDateString(),
                'status' => 'menunggu',
                'payment_status' => 'lunas',
                'payment_method' => 'qris_telu_pay',
                'payment_time' => Carbon::now()->subMinutes(2),
                'subtotal' => 28000,
                'service_fee' => 1000,
                'discount' => 0,
                'total_price' => 28000,
                'notes' => 'GAK PAKE SAYURAN, KUAHNYA JANGAN KERING!',
                'estimated_ready_time' => null,
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order2->id, 'menu_name' => 'Mie Aceh Daging Tumis (Basah / Nyemek)'],
            [
                'menu_id' => $menuMieAceh->id,
                'quantity' => 1,
                'unit_price' => 22000,
                'subtotal' => 22000,
                'spice_level' => 'Cabe Rawit 1-2 (Sedang)',
                'notes' => 'GAK PAKE SAYURAN',
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order2->id, 'menu_name' => 'Es Jeruk Peras Murni'],
            [
                'menu_id' => $menuJeruk->id,
                'quantity' => 1,
                'unit_price' => 6000,
                'subtotal' => 6000,
                'notes' => 'Es batu sedikit saja ya bu, tenggorok',
            ]
        );

        // Order 3: Ready for pickup (Siap Diambil)
        $order3 = Order::firstOrCreate(
            ['order_code' => '#TU-240499'],
            [
                'user_id' => $buyer3->id,
                'stall_id' => $stall1->id,
                'queue_number' => 'A-011',
                'order_date' => Carbon::now()->toDateString(),
                'status' => 'siap_diambil',
                'payment_status' => 'lunas',
                'payment_method' => 'qris_telu_pay',
                'payment_time' => Carbon::now()->subMinutes(15),
                'subtotal' => 34000,
                'service_fee' => 1000,
                'discount' => 0,
                'total_price' => 34000,
                'notes' => 'Bungkus plastik',
                'estimated_ready_time' => 'Pesanan siap di etalase',
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order3->id, 'menu_name' => 'Nasi Goreng Spesial Telur Ceplok'],
            [
                'menu_id' => null,
                'quantity' => 2,
                'unit_price' => 14000,
                'subtotal' => 28000,
                'notes' => null,
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order3->id, 'menu_name' => 'Es Jeruk Peras Murni'],
            [
                'menu_id' => $menuJeruk->id,
                'quantity' => 1,
                'unit_price' => 6000,
                'subtotal' => 6000,
                'notes' => null,
            ]
        );

        // 7. Sample Seller Registration Request
        SellerRequest::firstOrCreate(
            ['email' => 'batagor.ismail@gmail.com'],
            [
                'applicant_name' => 'Ismail Marzuki',
                'phone' => '085712348899',
                'stall_name' => 'Batagor & Siomay H. Ismail',
                'stall_code' => 'B-02',
                'stall_location' => 'Kantin Terpadu Gd. Selaru Lt. 1',
                'stall_description' => 'Batagor renyah bumbu kacang medok khas Bandung dengan ikan tenggiri asli.',
                'opening_hours' => '09:00 - 16:30 WIB',
                'contact_phone' => '085712348899',
                'bank_info' => 'BSI 7123984712 a.n Ismail Marzuki',
                'status' => 'menunggu',
            ]
        );
    }
}
