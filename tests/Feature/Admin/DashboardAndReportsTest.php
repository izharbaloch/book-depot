<?php

use App\Livewire\Admin\Reports;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

function actingAsReportsAdmin(): User
{
    test()->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    test()->actingAs($admin);

    return $admin;
}

test('dashboard shows today\'s combined sales and stock alerts', function () {
    $admin = actingAsReportsAdmin();
    $category = Category::create(['name' => 'Novels', 'slug' => 'novels', 'is_active' => true]);
    $product = Product::create([
        'category_id' => $category->id, 'name' => 'Dashboard Test Book', 'slug' => 'dashboard-test-book',
        'price' => 500, 'sku' => 'BKD-DASHTEST', 'stock' => 1, 'min_stock_level' => 5,
    ]);

    Sale::create([
        'sale_number' => Sale::generateSaleNumber(), 'user_id' => $admin->id, 'subtotal' => 500,
        'total' => 500, 'payment_method' => 'cash', 'amount_paid' => 500,
    ]);

    $response = $this->get(route('admin.dashboard'));

    $response->assertOk()
        ->assertSeeText("Today's Sales")
        ->assertSeeText('Low Stock')
        ->assertSeeText('Dashboard Test Book');
});

test('sales report aggregates online and pos totals for the selected date range', function () {
    actingAsReportsAdmin();
    $cashier = User::factory()->create();

    Order::create([
        'order_number' => Order::generateOrderNumber(), 'status' => 'pending', 'payment_method' => 'cod',
        'payment_status' => 'unpaid', 'first_name' => 'A', 'last_name' => 'B', 'email' => 'a@example.com',
        'address' => 'x', 'city' => 'x', 'zip_code' => 'x', 'subtotal' => 400, 'total' => 400,
    ]);

    Sale::create([
        'sale_number' => Sale::generateSaleNumber(), 'user_id' => $cashier->id, 'subtotal' => 600,
        'total' => 600, 'payment_method' => 'cash', 'amount_paid' => 600,
    ]);

    Livewire::test(Reports::class)
        ->call('setTab', 'sales')
        ->call('setPreset', 'today')
        ->assertSee('1,000.00'); // 400 + 600 combined grand total, formatted
});

test('product sales report sums quantity across both channels', function () {
    actingAsReportsAdmin();
    $cashier = User::factory()->create();
    $category = Category::create(['name' => 'Novels', 'slug' => 'novels', 'is_active' => true]);
    $product = Product::create([
        'category_id' => $category->id, 'name' => 'Cross Channel Book', 'slug' => 'cross-channel-book',
        'price' => 100, 'sku' => 'BKD-CROSSCHANNEL', 'stock' => 50,
    ]);

    $order = Order::create([
        'order_number' => Order::generateOrderNumber(), 'status' => 'pending', 'payment_method' => 'cod',
        'payment_status' => 'unpaid', 'first_name' => 'A', 'last_name' => 'B', 'email' => 'a@example.com',
        'address' => 'x', 'city' => 'x', 'zip_code' => 'x', 'subtotal' => 200, 'total' => 200,
    ]);
    \App\Models\OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 2, 'price' => 100, 'subtotal' => 200]);

    $sale = Sale::create([
        'sale_number' => Sale::generateSaleNumber(), 'user_id' => $cashier->id, 'subtotal' => 300,
        'total' => 300, 'payment_method' => 'cash', 'amount_paid' => 300,
    ]);
    SaleItem::create(['sale_id' => $sale->id, 'product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 3, 'price' => 100, 'subtotal' => 300]);

    Livewire::test(Reports::class)
        ->call('setTab', 'products')
        ->call('setPreset', 'today')
        ->assertSee('Cross Channel Book')
        ->assertSee('5'); // 2 + 3 combined quantity
});
