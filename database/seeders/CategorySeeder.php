<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'School Books',     'description' => 'Textbooks and guides for school students.', 'sort_order' => 1],
            ['name' => 'College Books',    'description' => 'Textbooks and guides for college students.', 'sort_order' => 2],
            ['name' => 'University Books', 'description' => 'Academic and reference books for university students.', 'sort_order' => 3],
            ['name' => 'Islamic Books',    'description' => 'Quran, Hadith, and Islamic literature.', 'sort_order' => 4],
            ['name' => 'Novels',           'description' => 'Fiction and literature.', 'sort_order' => 5],
            ['name' => 'Children Books',   'description' => 'Story books and learning books for kids.', 'sort_order' => 6],
            ['name' => 'Stationery',       'description' => 'Pens, notebooks, and everyday stationery.', 'sort_order' => 7],
            ['name' => 'Office Supplies',  'description' => 'Supplies for office and study use.', 'sort_order' => 8],
            ['name' => 'General Books',    'description' => 'Everything else worth reading.', 'sort_order' => 9],
        ];

        foreach ($categories as $cat) {
            Category::create(array_merge($cat, [
                'slug'      => Str::slug($cat['name']),
                'is_active' => true,
            ]));
        }
    }
}
