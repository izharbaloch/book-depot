<?php

use App\Livewire\Admin\Purchases;
use App\Livewire\Admin\Suppliers;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

function actingAsInventoryAdmin(): User
{
    Role::firstOrCreate(['name' => 'admin']);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    test()->actingAs($admin);

    return $admin;
}

test('admin can create a supplier', function () {
    actingAsInventoryAdmin();

    Livewire::test(Suppliers::class)
        ->call('openCreate')
        ->set('name', 'Test Supplier')
        ->set('phone', '0300-1234567')
        ->call('save')
        ->assertHasNoErrors();

    expect(Supplier::where('name', 'Test Supplier')->exists())->toBeTrue();
});

test('receiving stock through the purchases page increases product stock and logs a movement', function () {
    actingAsInventoryAdmin();

    $category = Category::create(['name' => 'Novels', 'slug' => 'novels', 'is_active' => true]);
    $supplier = Supplier::create(['name' => 'Test Supplier', 'is_active' => true]);
    $product  = Product::create([
        'category_id' => $category->id,
        'name'        => 'Stock Test Book',
        'slug'        => 'stock-test-book',
        'price'       => 500,
        'sku'         => 'BKD-STOCKTEST',
        'stock'       => 10,
    ]);

    Livewire::test(Purchases::class)
        ->call('openCreate')
        ->set('supplier_id', (string) $supplier->id)
        ->set('purchase_date', now()->toDateString())
        ->set('items.0.product_id', (string) $product->id)
        ->set('items.0.quantity', '15')
        ->set('items.0.purchase_price', '300')
        ->call('save')
        ->assertHasNoErrors();

    expect($product->fresh()->stock)->toBe(25);
    expect($product->stockMovements()->where('type', 'purchase')->count())->toBe(1);
    expect($product->stockMovements()->first()->balance_after)->toBe(25);
});

test('inventory service refuses to oversell a product', function () {
    $category = Category::create(['name' => 'Novels', 'slug' => 'novels', 'is_active' => true]);
    $product  = Product::create([
        'category_id' => $category->id,
        'name'        => 'Low Stock Book',
        'slug'        => 'low-stock-book',
        'price'       => 500,
        'sku'         => 'BKD-LOWSTOCK',
        'stock'       => 2,
    ]);

    expect(fn() => app(InventoryService::class)->adjust($product, -5, 'pos_sale'))
        ->toThrow(RuntimeException::class);

    expect($product->fresh()->stock)->toBe(2);
});
