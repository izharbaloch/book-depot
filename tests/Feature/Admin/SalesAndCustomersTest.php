<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Spatie\Permission\Models\Role;

function actingAsSalesAdmin(): User
{
    test()->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    test()->actingAs($admin);

    return $admin;
}

test('sales page merges pos and online sales into one combined list', function () {
    $admin = actingAsSalesAdmin();
    $category = Category::create(['name' => 'Novels', 'slug' => 'novels', 'is_active' => true]);
    $product = Product::create([
        'category_id' => $category->id, 'name' => 'Sales Test Book', 'slug' => 'sales-test-book',
        'price' => 500, 'sku' => 'BKD-SALESTEST', 'stock' => 20,
    ]);

    Order::create([
        'order_number' => Order::generateOrderNumber(), 'status' => 'pending', 'payment_method' => 'cod',
        'payment_status' => 'unpaid', 'first_name' => 'A', 'last_name' => 'B', 'email' => 'a@example.com',
        'address' => 'x', 'city' => 'x', 'zip_code' => 'x', 'subtotal' => 500, 'total' => 500,
    ]);

    $sale = Sale::create([
        'sale_number' => Sale::generateSaleNumber(), 'user_id' => $admin->id, 'subtotal' => 500,
        'total' => 500, 'payment_method' => 'cash', 'amount_paid' => 500,
    ]);
    SaleItem::create(['sale_id' => $sale->id, 'product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1, 'price' => 500, 'subtotal' => 500]);

    $response = $this->get(route('admin.sales.index'));

    $response->assertOk()
        ->assertSeeText('Transactions')
        ->assertSee($sale->sale_number)
        ->assertSee(Order::first()->order_number);
});

test('customers page shows cross-channel totals', function () {
    actingAsSalesAdmin();

    $customer = Customer::create(['name' => 'Repeat Customer', 'email' => 'repeat@example.com']);
    $category = Category::create(['name' => 'Novels', 'slug' => 'novels', 'is_active' => true]);

    Order::create([
        'customer_id' => $customer->id, 'order_number' => Order::generateOrderNumber(), 'status' => 'pending',
        'payment_method' => 'cod', 'payment_status' => 'unpaid', 'first_name' => 'Repeat', 'last_name' => 'Customer',
        'email' => 'repeat@example.com', 'address' => 'x', 'city' => 'x', 'zip_code' => 'x', 'subtotal' => 300, 'total' => 300,
    ]);

    $admin = User::where('email', '!=', null)->first();
    Sale::create([
        'customer_id' => $customer->id, 'sale_number' => Sale::generateSaleNumber(), 'user_id' => $admin->id,
        'subtotal' => 200, 'total' => 200, 'payment_method' => 'cash', 'amount_paid' => 200,
    ]);

    $response = $this->get(route('admin.customers.index'));

    $response->assertOk()
        ->assertSeeText('Repeat Customer')
        ->assertSeeText('$500.00'); // 300 + 200 combined spend
});
