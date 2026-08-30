<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            'Mark Twain',
            'Jane Austen',
            'Leo Tolstoy',
            'Saadat Hasan Manto',
            'Allama Muhammad Iqbal',
            'Maulana Wahiduddin Khan',
            'Roald Dahl',
            'Enid Blyton',
            'Punjab Curriculum & Textbook Board',
            'Various Authors',
        ];

        foreach ($authors as $name) {
            Author::create(['name' => $name, 'is_active' => true]);
        }
    }
}
