<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Ferozsons Distributors', 'phone' => '042-111222333', 'email' => 'sales@ferozsons.example'],
            ['name' => 'National Book Traders', 'phone' => '021-345678900', 'email' => 'orders@nbtraders.example'],
            ['name' => 'City Stationery Wholesale', 'phone' => '042-99887766', 'email' => 'info@citystationery.example'],
        ];

        foreach ($suppliers as $s) {
            Supplier::create(array_merge($s, ['is_active' => true]));
        }
    }
}
