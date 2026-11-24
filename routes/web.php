<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\MerchantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SMPEK Kantin Terpadu Telkom University
|--------------------------------------------------------------------------
*/

// Public / Landing / Onboarding
Route::get('/', [BuyerController::class, 'onboarding'])->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::match(['GET', 'POST'], '/logout', [AuthController::class, 'logout'])->name('logout');

// User Profile & Biodata Routes (All Authenticated Roles: Pembeli, Penjual, Admin)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [BuyerController::class, 'profile'])->name('profile.index');
    Route::post('/profile', [BuyerController::class, 'updateProfile'])->name('profile.update');
    Route::get('/cart', [BuyerController::class, 'cart'])->name('cart.index');
    Route::get('/orders', [BuyerController::class, 'orders'])->name('orders.index');
});

// Catalog (Accessible to both guests and buyers)
Route::get('/menu', [BuyerController::class, 'menuCatalog'])->name('menu.index');
Route::get('/menu/{id}', [BuyerController::class, 'menuDetail'])->name('menu.show');
Route::get('/daftar-penjual', [BuyerController::class, 'showSellerRegistration'])->name('seller.register.form');
Route::post('/daftar-penjual', [BuyerController::class, 'submitSellerRegistration'])->name('seller.register.submit');

// Public Scan-to-Pay for Google Lens & Status API
Route::get('/pay/{order_code}', [BuyerController::class, 'processScanPayment'])->name('orders.pay.scan');
Route::get('/api/orders/{id}/status', [BuyerController::class, 'orderStatusApi'])->name('orders.status.api');

// Buyer Protected Routes
Route::middleware(['auth', 'role:pembeli'])->group(function () {
    Route::post('/cart/add', [BuyerController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update/{id}', [BuyerController::class, 'updateCart'])->name('cart.update');
    Route::post('/cart/remove/{id}', [BuyerController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/clear', [BuyerController::class, 'clearCart'])->name('cart.clear');
    Route::post('/checkout', [BuyerController::class, 'checkout'])->name('checkout.process');

    Route::get('/orders/{id}/qris', [BuyerController::class, 'showQrisPayment'])->name('orders.qris');
    Route::get('/orders/{id}', [BuyerController::class, 'orderStatus'])->name('orders.show');
    Route::get('/orders/{id}/receipt', [BuyerController::class, 'receipt'])->name('orders.receipt');
});

// Merchant / Penjual Protected Routes
Route::middleware(['auth', 'role:penjual'])->prefix('merchant')->name('merchant.')->group(function () {
    Route::get('/', [MerchantController::class, 'dashboard'])->name('dashboard');
    Route::post('/orders/{id}/confirm', [MerchantController::class, 'confirmPayment'])->name('orders.confirm');
    Route::post('/orders/{id}/status', [MerchantController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/complete-all', [MerchantController::class, 'completeAllReadyOrders'])->name('orders.complete-all');

    Route::get('/menus', [MerchantController::class, 'menus'])->name('menus.index');
    Route::post('/menus', [MerchantController::class, 'storeMenu'])->name('menus.store');
    Route::post('/menus/clear-all-stock', [MerchantController::class, 'clearAllStock'])->name('menus.clear-all-stock');
    Route::post('/menus/{id}', [MerchantController::class, 'updateMenu'])->name('menus.update');
    Route::post('/menus/{id}/stock', [MerchantController::class, 'updateStock'])->name('menus.stock');
    Route::post('/menus/{id}/delete', [MerchantController::class, 'deleteMenu'])->name('menus.delete');

    Route::get('/reports', [MerchantController::class, 'reports'])->name('reports');
    Route::get('/stall/profile', [MerchantController::class, 'stallProfile'])->name('stall.profile');
    Route::post('/stall/profile', [MerchantController::class, 'updateStallProfile'])->name('stall.update');
    Route::post('/toggle-status', [MerchantController::class, 'toggleStallStatus'])->name('stall.toggle');
});

// Admin Protected Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/sellers', [AdminController::class, 'storeSeller'])->name('sellers.store');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::post('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('users.delete');

    Route::post('/seller-requests/{id}/approve', [AdminController::class, 'approveSellerRequest'])->name('seller-requests.approve');
    Route::post('/seller-requests/{id}/reject', [AdminController::class, 'rejectSellerRequest'])->name('seller-requests.reject');

    Route::get('/transactions', [AdminController::class, 'monitorTransactions'])->name('transactions');
    Route::get('/stalls-categories', [AdminController::class, 'stallsAndCategories'])->name('stalls.categories');
    Route::post('/stalls', [AdminController::class, 'storeStall'])->name('stalls.store');
    Route::post('/stalls/{id}', [AdminController::class, 'updateStall'])->name('stalls.update');
    Route::post('/stalls/{id}/delete', [AdminController::class, 'deleteStall'])->name('stalls.delete');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::post('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::post('/categories/{id}/delete', [AdminController::class, 'deleteCategory'])->name('categories.delete');
    Route::get('/reports', [AdminController::class, 'salesReport'])->name('reports');
});
