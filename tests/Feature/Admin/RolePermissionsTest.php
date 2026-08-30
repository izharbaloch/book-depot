<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

function staffUser(string $role): User
{
    $user = User::factory()->create();
    $user->assignRole($role);
    test()->actingAs($user);

    return $user;
}

test('cashier can reach pos and sales but not products or reports', function () {
    staffUser('cashier');

    $this->get(route('admin.pos.index'))->assertOk();
    $this->get(route('admin.sales.index'))->assertOk();
    $this->get(route('admin.customers.index'))->assertOk();

    $this->get(route('admin.products.index'))->assertForbidden();
    $this->get(route('admin.dashboard'))->assertForbidden();
    $this->get(route('admin.users.index'))->assertForbidden();
});

test('inventory staff can reach catalog and stock but not pos or orders', function () {
    staffUser('inventory_staff');

    $this->get(route('admin.products.index'))->assertOk();
    $this->get(route('admin.stock.index'))->assertOk();
    $this->get(route('admin.purchases.index'))->assertOk();
    $this->get(route('admin.suppliers.index'))->assertOk();

    $this->get(route('admin.pos.index'))->assertForbidden();
    $this->get(route('admin.orders.index'))->assertForbidden();
    $this->get(route('admin.dashboard'))->assertForbidden();
});

test('order staff can reach online orders and customers but not products or pos', function () {
    staffUser('order_staff');

    $this->get(route('admin.orders.index'))->assertOk();
    $this->get(route('admin.customers.index'))->assertOk();

    $this->get(route('admin.products.index'))->assertForbidden();
    $this->get(route('admin.pos.index'))->assertForbidden();
    $this->get(route('admin.sales.index'))->assertForbidden();
});

test('admin can reach every admin section', function () {
    staffUser('admin');

    $this->get(route('admin.dashboard'))->assertOk();
    $this->get(route('admin.products.index'))->assertOk();
    $this->get(route('admin.pos.index'))->assertOk();
    $this->get(route('admin.orders.index'))->assertOk();
    $this->get(route('admin.sales.index'))->assertOk();
    $this->get(route('admin.customers.index'))->assertOk();
    $this->get(route('admin.users.index'))->assertOk();
    $this->get(route('admin.reports.index'))->assertOk();
});
