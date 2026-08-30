<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@bookdepot.test',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Staff accounts, one per role
        $staff = [
            ['name' => 'Cashier One',      'email' => 'cashier@bookdepot.test',   'role' => 'cashier'],
            ['name' => 'Inventory Staff',  'email' => 'inventory@bookdepot.test', 'role' => 'inventory_staff'],
            ['name' => 'Order Staff',      'email' => 'orders@bookdepot.test',    'role' => 'order_staff'],
        ];
        foreach ($staff as $s) {
            $createdStaff = User::create([
                'name'     => $s['name'],
                'email'    => $s['email'],
                'password' => Hash::make('password'),
            ]);
            $createdStaff->assignRole($s['role']);
        }

        // Demo customer
        $user = User::create([
            'name'     => 'Amara Khan',
            'email'    => 'user@bookdepot.test',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('user');

        // Extra customers
        $users = [
            ['name' => 'Lucas M',   'email' => 'lucas@example.com'],
            ['name' => 'Sofia R',   'email' => 'sofia@example.com'],
            ['name' => 'James W',   'email' => 'james@example.com'],
        ];
        foreach ($users as $u) {
            $createdUser = User::create(array_merge($u, [
                'password' => Hash::make('password'),
            ]));
            $createdUser->assignRole('user');
        }
    }
}
