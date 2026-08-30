<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminPosController;
use App\Livewire\Admin\Products as AdminProducts;
use App\Livewire\Admin\Categories as AdminCategories;
use App\Livewire\Admin\Authors as AdminAuthors;
use App\Livewire\Admin\Publishers as AdminPublishers;
use App\Livewire\Admin\Suppliers as AdminSuppliers;
use App\Livewire\Admin\Purchases as AdminPurchases;
use App\Livewire\Admin\Stock as AdminStock;
use App\Livewire\Admin\Pos as AdminPos;
use App\Livewire\Admin\Sales as AdminSales;
use App\Livewire\Admin\Customers as AdminCustomers;
use App\Livewire\Admin\Reports as AdminReports;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/product/{product}/quick-view', [ProductController::class, 'quickView'])->name('product.quickview');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/orders',           [DashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}',   [DashboardController::class, 'orderDetail'])->name('orders.detail');
    Route::get('/wishlist',         [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/checkout',         [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout',        [CheckoutController::class, 'placeOrder'])->name('checkout.place');
    Route::get('/order/success/{order}', [CheckoutController::class, 'success'])->name('order.success');


    Route::prefix('admin')->name('admin.')->middleware(['role:admin|cashier|inventory_staff|order_staff'])->group(function () {
        // Dashboard & Reports (admin only)
        Route::middleware('permission:manage reports')->group(function () {
            Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
            Route::get('reports', AdminReports::class)->name('reports.index');
        });

        // Catalog (inventory staff + admin)
        Route::middleware('permission:manage products')->group(function () {
            Route::get('products', AdminProducts::class)->name('products.index');
            Route::get('categories', AdminCategories::class)->name('categories.index');
            Route::get('authors', AdminAuthors::class)->name('authors.index');
            Route::get('publishers', AdminPublishers::class)->name('publishers.index');
        });

        // Inventory (inventory staff + admin)
        Route::middleware('permission:manage stock')->group(function () {
            Route::get('stock', AdminStock::class)->name('stock.index');
        });
        Route::middleware('permission:manage purchases')->group(function () {
            Route::get('purchases', AdminPurchases::class)->name('purchases.index');
        });
        Route::middleware('permission:manage suppliers')->group(function () {
            Route::get('suppliers', AdminSuppliers::class)->name('suppliers.index');
        });

        // POS (cashier + admin)
        Route::middleware('permission:access pos')->group(function () {
            Route::get('pos', AdminPos::class)->name('pos.index');
            Route::get('pos/receipt/{sale}', [AdminPosController::class, 'receipt'])->name('pos.receipt');
        });

        // Online Orders (order staff + admin)
        Route::middleware('permission:manage orders')->group(function () {
            Route::get('orders',                    [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}',            [AdminOrderController::class, 'show'])->name('orders.show');
            Route::patch('orders/{order}/status',   [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        });

        // Sales — combined POS + Online (cashier + admin)
        Route::middleware('permission:manage sales')->group(function () {
            Route::get('sales', AdminSales::class)->name('sales.index');
        });

        // Customers (cashier, order staff + admin)
        Route::middleware('permission:manage customers')->group(function () {
            Route::get('customers', AdminCustomers::class)->name('customers.index');
        });

        // Users (admin only)
        Route::middleware('permission:manage users')->group(function () {
            Route::get('users',       [AdminUserController::class, 'index'])->name('users.index');
            Route::get('users/{user}', [AdminUserController::class, 'show'])->name('users.show');
            Route::patch('users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
        });
    });
});

require __DIR__ . '/auth.php';
