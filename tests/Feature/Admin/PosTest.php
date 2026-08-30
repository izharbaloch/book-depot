<?php

use App\Livewire\Admin\Pos;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

function actingAsCashier(): User
{
    Role::firstOrCreate(['name' => 'cashier']);
    $cashier = User::factory()->create();
    $cashier->assignRole('cashier');
    test()->actingAs($cashier);

    return $cashier;
}

function makeSellableProduct(int $stock = 5): Product
{
    $category = Category::create(['name' => 'Novels', 'slug' => 'novels', 'is_active' => true]);

    return Product::create([
        'category_id' => $category->id,
        'name'        => 'POS Test Book',
        'slug'        => 'pos-test-book',
        'price'       => 500,
        'sku'         => 'BKD-POSTEST',
        'stock'       => $stock,
        'is_active'   => true,
    ]);
}

test('cashier can complete a walk-in cash sale and stock is deducted exactly once', function () {
    actingAsCashier();
    $product = makeSellableProduct(10);

    Livewire::test(Pos::class)
        ->call('addToCart', $product->id)
        ->set('paymentMethod', 'cash')
        ->set('amountPaid', '1000')
        ->call('completeSale')
        ->assertSet('cartError', null);

    expect($product->fresh()->stock)->toBe(9);
    expect(Sale::count())->toBe(1);

    $sale = Sale::first();
    expect((float) $sale->total)->toBe(500.0);
    expect($sale->customer_id)->toBeNull();
    expect((float) $sale->change_amount)->toBe(500.0);
});

test('pos refuses to add more to the cart than available stock', function () {
    actingAsCashier();
    $product = makeSellableProduct(2);

    $component = Livewire::test(Pos::class)
        ->call('addToCart', $product->id)
        ->call('incrementQty', $product->id)
        ->call('incrementQty', $product->id);

    expect($component->get('cart')[$product->id]['quantity'])->toBe(2);
    expect($component->get('cartError'))->not->toBeNull();
});

test('cash sale is rejected when amount paid is less than total', function () {
    actingAsCashier();
    $product = makeSellableProduct(5);

    Livewire::test(Pos::class)
        ->call('addToCart', $product->id)
        ->set('paymentMethod', 'cash')
        ->set('amountPaid', '100')
        ->call('completeSale')
        ->assertSet('cartError', 'Amount paid is less than the total due.');

    expect(Sale::count())->toBe(0);
    expect($product->fresh()->stock)->toBe(5);
});

test('card payment does not require an amount paid input and leaves no change', function () {
    actingAsCashier();
    $product = makeSellableProduct(5);

    Livewire::test(Pos::class)
        ->call('addToCart', $product->id)
        ->set('paymentMethod', 'card')
        ->call('completeSale');

    $sale = Sale::first();
    expect((float) $sale->amount_paid)->toBe(500.0);
    expect((float) $sale->change_amount)->toBe(0.0);
});
