<?php

use App\Livewire\Admin\Authors;
use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Products;
use App\Livewire\Admin\Publishers;
use App\Models\Author;
use App\Models\Category;
use App\Models\Product;
use App\Models\Publisher;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

function actingAsAdmin(): User
{
    test()->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    test()->actingAs($admin);

    return $admin;
}

test('admin can view the products, categories, authors, and publishers pages', function () {
    actingAsAdmin();

    $this->get(route('admin.products.index'))->assertOk()->assertSeeText('All Products');
    $this->get(route('admin.categories.index'))->assertOk()->assertSeeText('All Categories');
    $this->get(route('admin.authors.index'))->assertOk()->assertSeeText('All Authors');
    $this->get(route('admin.publishers.index'))->assertOk()->assertSeeText('All Publishers');
});

test('admin can create a product through the same-page Livewire form', function () {
    actingAsAdmin();

    $category = Category::create(['name' => 'Novels', 'slug' => 'novels', 'is_active' => true]);
    $author = Author::create(['name' => 'Mark Twain', 'is_active' => true]);
    $publisher = Publisher::create(['name' => 'Penguin', 'is_active' => true]);

    Livewire::test(Products::class)
        ->call('openCreate')
        ->set('name', 'Test Novel')
        ->set('category_id', (string) $category->id)
        ->set('author_id', (string) $author->id)
        ->set('publisher_id', (string) $publisher->id)
        ->set('price', '500')
        ->set('stock', '10')
        ->set('min_stock_level', '3')
        ->call('save')
        ->assertHasNoErrors();

    expect(Product::where('name', 'Test Novel')->exists())->toBeTrue();
});

test('duplicate SKU is rejected on the product form', function () {
    actingAsAdmin();

    $category = Category::create(['name' => 'Novels', 'slug' => 'novels', 'is_active' => true]);

    Product::create([
        'category_id' => $category->id,
        'name'        => 'Existing Book',
        'slug'        => 'existing-book',
        'price'       => 100,
        'sku'         => 'BKD-DUPLICATE',
        'stock'       => 5,
    ]);

    Livewire::test(Products::class)
        ->call('openCreate')
        ->set('name', 'Another Book')
        ->set('category_id', (string) $category->id)
        ->set('sku', 'BKD-DUPLICATE')
        ->set('price', '200')
        ->set('stock', '5')
        ->call('save')
        ->assertHasErrors(['sku']);
});

test('admin can create a category with a parent', function () {
    actingAsAdmin();

    $parent = Category::create(['name' => 'Books', 'slug' => 'books', 'is_active' => true]);

    Livewire::test(Categories::class)
        ->call('openCreate')
        ->set('name', 'Novels')
        ->set('parent_id', (string) $parent->id)
        ->call('save')
        ->assertHasNoErrors();

    $child = Category::where('name', 'Novels')->first();
    expect($child)->not->toBeNull();
    expect($child->parent_id)->toBe($parent->id);
});

test('admin can create and deactivate an author', function () {
    actingAsAdmin();

    Livewire::test(Authors::class)
        ->call('openCreate')
        ->set('name', 'Jane Austen')
        ->call('save')
        ->assertHasNoErrors();

    $author = Author::where('name', 'Jane Austen')->firstOrFail();

    Livewire::test(Authors::class)
        ->call('toggleActive', $author->id);

    expect($author->fresh()->is_active)->toBeFalse();
});
