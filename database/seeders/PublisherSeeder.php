<?php

namespace Database\Seeders;

use App\Models\Publisher;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    public function run(): void
    {
        $publishers = [
            'Ferozsons (Pvt) Ltd',
            'Ilmi Kitab Khana',
            'Oxford University Press Pakistan',
            'Paramount Books',
            'Sang-e-Meel Publications',
            'Liberty Books',
            'Penguin Random House',
            'HarperCollins',
        ];

        foreach ($publishers as $name) {
            Publisher::create(['name' => $name, 'is_active' => true]);
        }
    }
}
