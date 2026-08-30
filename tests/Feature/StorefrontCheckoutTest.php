<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Spatie\Permission\Models\Role;

function makeCheckoutProduct(int $stock = 10): Product
{
    $category = Category::create(['name' => 'Novels', 'slug' => 'novels', 'is_active' => true]);

    return Product::create([
        'category_id' => $category->id,
        'name'        => 'Checkout Test Book',
        'slug'        => 'checkout-test-book',
        'price'       => 500,
        'sku'         => 'BKD-CHECKOUTTEST',
        'stock'       => $stock,
        'is_active'   => true,
    ]);
}

function checkoutPayload(): array
{
    return [
        'first_name'     => 'Test',
        'last_name'      => 'Buyer',
        'email'          => 'buyer@example.com',
        'phone'          => '0300-1234567',
        'address'        => '123 Main St',
        'city'           => 'Lahore',
        'state'          => 'Punjab',
        'zip_code'       => '54000',
        'country'        => 'Pakistan',
        'payment_method' => 'cod',
        'notes'          => '',
    ];
}

test('placing an online order deducts stock and links a customer', function () {
    $user = User::factory()->create();
    Role::firstOrCreate(['name' => 'user']);
    $user->assignRole('user');
    $this->actingAs($user);

    $product = makeCheckoutProduct(10);

    $cart = \App\Models\Cart::getForUser();
    \App\Models\CartItem::create([
        'cart_id'    => $cart->id,
        'product_id' => $product->id,
        'quantity'   => 3,
        'price'      => $product->price,
    ]);

    $response = $this->post('/checkout', checkoutPayload());

    $order = Order::first();
    expect($order)->not->toBeNull();
    expect($response->status())->toBe(302);
    expect($product->fresh()->stock)->toBe(7);
    expect($order->customer_id)->not->toBeNull();
    expect($order->customer->email)->toBe('buyer@example.com');
});

test('checkout refuses to place an order that exceeds available stock', function () {
    $user = User::factory()->create();
    Role::firstOrCreate(['name' => 'user']);
    $user->assignRole('user');
    $this->actingAs($user);

    $product = makeCheckoutProduct(2);

    $cart = \App\Models\Cart::getForUser();
    \App\Models\CartItem::create([
        'cart_id'    => $cart->id,
        'product_id' => $product->id,
        'quantity'   => 2,
        'price'      => $product->price,
    ]);
    // Force stock down after cart was created to simulate a race with another sale.
    $product->update(['stock' => 1]);

    $this->post('/checkout', checkoutPayload())->assertRedirect('/checkout');

    expect(Order::count())->toBe(0);
    expect($product->fresh()->stock)->toBe(1);
});

test('cancelling an online order restores stock exactly once', function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $product = makeCheckoutProduct(5);
    $product->update(['stock' => 5]);

    $order = Order::create([
        'order_number'   => Order::generateOrderNumber(),
        'status'         => 'pending',
        'payment_method' => 'cod',
        'payment_status' => 'unpaid',
        'first_name'     => 'Test',
        'last_name'      => 'Buyer',
        'email'          => 'buyer@example.com',
        'address'        => '123 Main St',
        'city'           => 'Lahore',
        'zip_code'       => '54000',
        'subtotal'       => 1000,
        'total'          => 1000,
    ]);
    \App\Models\OrderItem::create([
        'order_id'     => $order->id,
        'product_id'   => $product->id,
        'product_name' => $product->name,
        'quantity'     => 2,
        'price'        => 500,
        'subtotal'     => 1000,
    ]);
    app(\App\Services\InventoryService::class)->adjust($product, -2, 'online_sale', $order);
    expect($product->fresh()->stock)->toBe(3);

    $this->patch(route('admin.orders.status', $order), ['status' => 'cancelled']);

    expect($product->fresh()->stock)->toBe(5);

    // Cancelling again (already cancelled) must not double-restore stock.
    $this->patch(route('admin.orders.status', $order), ['status' => 'cancelled']);
    expect($product->fresh()->stock)->toBe(5);
});
