<?php

use App\Livewire\Admin\Pos;
use App\Livewire\Admin\Purchases;
use App\Livewire\Admin\Suppliers;
use App\Models\Category;
use App\Models\Product;
use App\Models\SaleItem;
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
        'cost_price'  => 200,
        'sku'         => 'BKD-STOCKTEST',
        'stock'       => 10,
    ]);

    Livewire::test(Purchases::class)
        ->call('openCreate')
        ->set('supplier_id', (string) $supplier->id)
        ->set('purchase_date', now()->toDateString())
        ->call('addProduct', $product->id)
        ->set('items.0.quantity', '15')
        ->set('items.0.purchase_price', '300')
        ->call('save')
        ->assertHasNoErrors();

    expect($product->fresh()->stock)->toBe(25);
    expect($product->stockMovements()->where('type', 'purchase')->count())->toBe(1);
    expect($product->stockMovements()->first()->balance_after)->toBe(25);
    // Weighted average: (10 @ 200 + 15 @ 300) / 25 = 260.00
    expect((float) $product->fresh()->cost_price)->toBe(260.0);
});

test('weighted average cost across purchases at different rates flows through to sale profit', function () {
    actingAsInventoryAdmin();

    $category = Category::create(['name' => 'Stationery', 'slug' => 'stationery', 'is_active' => true]);
    $supplier = Supplier::create(['name' => 'Blue Pen Supplier', 'is_active' => true]);
    $product  = Product::create([
        'category_id' => $category->id,
        'name'        => 'Blue Pen',
        'slug'        => 'blue-pen',
        'price'       => 150,
        'sku'         => 'BKD-BLUEPEN',
        'stock'       => 0,
    ]);

    // Purchase #1: 100 @ Rs.100
    Livewire::test(Purchases::class)
        ->call('openCreate')
        ->set('supplier_id', (string) $supplier->id)
        ->set('purchase_date', now()->toDateString())
        ->call('addProduct', $product->id)
        ->set('items.0.quantity', '100')
        ->set('items.0.purchase_price', '100')
        ->call('save')
        ->assertHasNoErrors();

    // Purchase #2: 50 @ Rs.110
    Livewire::test(Purchases::class)
        ->call('openCreate')
        ->set('supplier_id', (string) $supplier->id)
        ->set('purchase_date', now()->toDateString())
        ->call('addProduct', $product->id)
        ->set('items.0.quantity', '50')
        ->set('items.0.purchase_price', '110')
        ->call('save')
        ->assertHasNoErrors();

    $product->refresh();
    expect($product->stock)->toBe(150);
    expect((float) $product->cost_price)->toBe(103.33);
    // Both original purchase rates remain untouched in history.
    expect($product->purchaseItems()->pluck('purchase_price')->map(fn($p) => (float) $p)->sort()->values()->toArray())
        ->toBe([100.0, 110.0]);

    // Sell 10 units via POS at the Rs.150 sale price.
    Livewire::test(Pos::class)
        ->call('addToCart', $product->id)
        ->set('cart.' . $product->id . '.quantity', 10)
        ->set('paymentMethod', 'cash')
        ->set('amountPaid', '1500')
        ->call('completeSale')
        ->assertHasNoErrors();

    $saleItem = SaleItem::where('product_id', $product->id)->firstOrFail();
    expect((float) $saleItem->cost_price)->toBe(103.33);
    expect((float) $saleItem->subtotal)->toBe(1500.0);

    $revenue = 150 * 10;
    $cost    = round(103.33 * 10, 2);
    expect(round($revenue - $cost, 2))->toBe(466.70);
});

test('purchase screen product search and inline new-product form render and work', function () {
    actingAsInventoryAdmin();

    $category = Category::create(['name' => 'Novels', 'slug' => 'novels', 'is_active' => true]);
    $supplier = Supplier::create(['name' => 'Test Supplier', 'is_active' => true]);
    $existing = Product::create([
        'category_id' => $category->id, 'name' => 'Searchable Book', 'slug' => 'searchable-book',
        'price' => 500, 'sku' => 'BKD-SEARCHME', 'stock' => 5, 'cost_price' => 150,
    ]);

    $component = Livewire::test(Purchases::class)
        ->call('openCreate')
        ->set('supplier_id', (string) $supplier->id)
        ->set('purchase_date', now()->toDateString())
        // Search dropdown renders a matching product.
        ->set('itemSearch', 'Searchable')
        ->assertSee('Searchable Book')
        ->assertSee('BKD-SEARCHME')
        ->call('addProduct', $existing->id)
        ->assertSet('items.0.product_id', $existing->id)
        // Inline "+ New Product" mini-form.
        ->call('openNewProduct')
        ->assertSee('Create & Add to Purchase')
        ->set('np_name', 'Fresh Off The Press')
        ->set('np_category_id', (string) $category->id)
        ->set('np_price', '750')
        ->call('saveNewProduct')
        ->assertHasNoErrors();

    $newProduct = Product::where('name', 'Fresh Off The Press')->firstOrFail();
    expect($newProduct->stock)->toBe(0);
    expect((float) $newProduct->price)->toBe(750.0);

    // Auto-added to the current purchase's line items.
    $component->assertSet('items.1.product_id', $newProduct->id);

    $component
        ->set('items.0.quantity', '5')
        ->set('items.0.purchase_price', '150')
        ->set('items.1.quantity', '2')
        ->set('items.1.purchase_price', '600')
        ->call('save')
        ->assertHasNoErrors();

    expect($existing->fresh()->stock)->toBe(10);
    expect($newProduct->fresh()->stock)->toBe(2);
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
