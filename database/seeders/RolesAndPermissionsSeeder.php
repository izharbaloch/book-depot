<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'access pos',
            'manage products',
            'manage stock',
            'manage purchases',
            'manage suppliers',
            'manage orders',
            'manage sales',
            'manage customers',
            'manage reports',
            'manage settings',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Storefront customer account — no admin-panel permissions.
        Role::firstOrCreate(['name' => 'user']);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions);

        $cashier = Role::firstOrCreate(['name' => 'cashier']);
        $cashier->syncPermissions(['access pos', 'manage sales', 'manage customers']);

        $inventoryStaff = Role::firstOrCreate(['name' => 'inventory_staff']);
        $inventoryStaff->syncPermissions(['manage products', 'manage stock', 'manage purchases', 'manage suppliers']);

        $orderStaff = Role::firstOrCreate(['name' => 'order_staff']);
        $orderStaff->syncPermissions(['manage orders', 'manage customers']);
    }
}
