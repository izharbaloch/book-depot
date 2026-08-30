<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Category;
use App\Models\Product;
use App\Models\Publisher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryId = fn(string $name) => Category::where('name', $name)->value('id');
        $authorId   = fn(string $name) => Author::where('name', $name)->value('id');
        $publisherId = fn(string $name) => Publisher::where('name', $name)->value('id');

        $school     = $categoryId('School Books');
        $college    = $categoryId('College Books');
        $university = $categoryId('University Books');
        $islamic    = $categoryId('Islamic Books');
        $novels     = $categoryId('Novels');
        $children   = $categoryId('Children Books');
        $stationery = $categoryId('Stationery');
        $office     = $categoryId('Office Supplies');
        $general    = $categoryId('General Books');

        $products = [
            // ── NOVELS ──────────────────────────────────────────
            [
                'category_id' => $novels, 'author_id' => $authorId('Mark Twain'), 'publisher_id' => $publisherId('Penguin Random House'),
                'name' => 'The Adventures of Tom Sawyer', 'isbn' => '9780143039563',
                'price' => 650, 'sale_price' => null, 'cost_price' => 420, 'stock' => 40, 'min_stock_level' => 8,
                'is_trending' => true, 'is_bestseller' => true, 'is_new' => false, 'is_featured' => true,
                'rating' => 4.7, 'review_count' => 128,
                'short_description' => 'A classic tale of boyhood adventure along the Mississippi.',
                'description' => 'Mark Twain\'s timeless novel following Tom Sawyer\'s escapades in a small Missouri town. A must-read classic of American literature.',
            ],
            [
                'category_id' => $novels, 'author_id' => $authorId('Jane Austen'), 'publisher_id' => $publisherId('Penguin Random House'),
                'name' => 'Pride and Prejudice', 'isbn' => '9780141439518',
                'price' => 720, 'sale_price' => 590, 'cost_price' => 460, 'stock' => 35, 'min_stock_level' => 8,
                'is_trending' => true, 'is_bestseller' => true, 'is_new' => false, 'is_featured' => true,
                'rating' => 4.8, 'review_count' => 210,
                'short_description' => 'Jane Austen\'s beloved novel of manners, marriage, and wit.',
                'description' => 'The story of Elizabeth Bennet and Mr. Darcy — one of the most cherished romances in English literature.',
            ],
            [
                'category_id' => $novels, 'author_id' => $authorId('Leo Tolstoy'), 'publisher_id' => $publisherId('HarperCollins'),
                'name' => 'Anna Karenina', 'isbn' => '9780143035008',
                'price' => 980, 'sale_price' => null, 'cost_price' => 640, 'stock' => 18, 'min_stock_level' => 5,
                'is_trending' => false, 'is_bestseller' => false, 'is_new' => false, 'is_featured' => false,
                'rating' => 4.6, 'review_count' => 54,
                'short_description' => 'A sweeping tragedy of love and society in Imperial Russia.',
                'description' => 'Leo Tolstoy\'s masterpiece exploring love, betrayal, and redemption across the highest circles of Russian society.',
            ],
            [
                'category_id' => $novels, 'author_id' => $authorId('Saadat Hasan Manto'), 'publisher_id' => $publisherId('Sang-e-Meel Publications'),
                'name' => 'Manto Ke Afsane', 'isbn' => '9789693513201',
                'price' => 495, 'sale_price' => null, 'cost_price' => 300, 'stock' => 26, 'min_stock_level' => 6,
                'is_trending' => false, 'is_bestseller' => true, 'is_new' => false, 'is_featured' => false,
                'rating' => 4.9, 'review_count' => 76,
                'short_description' => 'A powerful collection of short stories by Saadat Hasan Manto.',
                'description' => 'A collection of Manto\'s most acclaimed short stories, capturing the rawness of Partition-era South Asia.',
            ],

            // ── ISLAMIC BOOKS ───────────────────────────────────
            [
                'category_id' => $islamic, 'author_id' => null, 'publisher_id' => $publisherId('Ferozsons (Pvt) Ltd'),
                'name' => 'Quran Majeed (Arabic with Urdu Translation)', 'isbn' => '9789690021456',
                'price' => 850, 'sale_price' => null, 'cost_price' => 520, 'stock' => 60, 'min_stock_level' => 10,
                'is_trending' => true, 'is_bestseller' => true, 'is_new' => false, 'is_featured' => true,
                'rating' => 5.0, 'review_count' => 340,
                'short_description' => 'The Holy Quran with clear Urdu translation.',
                'description' => 'A beautifully printed Mus\'haf with Arabic text and accurate Urdu translation, ideal for daily recitation.',
            ],
            [
                'category_id' => $islamic, 'author_id' => $authorId('Maulana Wahiduddin Khan'), 'publisher_id' => $publisherId('Ilmi Kitab Khana'),
                'name' => 'Tazkirul Quran', 'isbn' => '9788178981234',
                'price' => 620, 'sale_price' => 550, 'cost_price' => 380, 'stock' => 22, 'min_stock_level' => 6,
                'is_trending' => false, 'is_bestseller' => false, 'is_new' => true, 'is_featured' => false,
                'rating' => 4.7, 'review_count' => 41,
                'short_description' => 'A modern commentary and translation of the Quran.',
                'description' => 'Maulana Wahiduddin Khan\'s accessible commentary bringing the message of the Quran to modern readers.',
            ],

            // ── SCHOOL / COLLEGE / UNIVERSITY ────────────────────
            [
                'category_id' => $school, 'author_id' => $authorId('Punjab Curriculum & Textbook Board'), 'publisher_id' => $publisherId('Ilmi Kitab Khana'),
                'name' => 'Mathematics for Class 9', 'isbn' => '9789693518234',
                'price' => 380, 'sale_price' => null, 'cost_price' => 220, 'stock' => 80, 'min_stock_level' => 15,
                'is_trending' => false, 'is_bestseller' => true, 'is_new' => false, 'is_featured' => false,
                'rating' => 4.3, 'review_count' => 58,
                'short_description' => 'Board-approved Class 9 mathematics textbook.',
                'description' => 'Comprehensive Class 9 mathematics textbook aligned with the national curriculum, with solved examples and practice exercises.',
            ],
            [
                'category_id' => $school, 'author_id' => $authorId('Punjab Curriculum & Textbook Board'), 'publisher_id' => $publisherId('Ilmi Kitab Khana'),
                'name' => 'English Grammar for Class 8', 'isbn' => '9789693518241',
                'price' => 340, 'sale_price' => null, 'cost_price' => 200, 'stock' => 65, 'min_stock_level' => 15,
                'is_trending' => false, 'is_bestseller' => false, 'is_new' => false, 'is_featured' => false,
                'rating' => 4.2, 'review_count' => 33,
                'short_description' => 'Grammar and composition guide for Class 8 students.',
                'description' => 'A clear, structured guide to English grammar and composition for middle-school students.',
            ],
            [
                'category_id' => $college, 'author_id' => $authorId('Various Authors'), 'publisher_id' => $publisherId('Oxford University Press Pakistan'),
                'name' => 'Intermediate Physics Part I', 'isbn' => '9780199065432',
                'price' => 720, 'sale_price' => null, 'cost_price' => 460, 'stock' => 30, 'min_stock_level' => 8,
                'is_trending' => false, 'is_bestseller' => false, 'is_new' => false, 'is_featured' => false,
                'rating' => 4.1, 'review_count' => 22,
                'short_description' => 'Intermediate-level physics textbook, Part I.',
                'description' => 'Covers mechanics, heat, and thermodynamics for intermediate (FSc Part I) students, with worked examples.',
            ],
            [
                'category_id' => $university, 'author_id' => $authorId('Various Authors'), 'publisher_id' => $publisherId('Oxford University Press Pakistan'),
                'name' => 'Principles of Economics', 'isbn' => '9780199057845',
                'price' => 1450, 'sale_price' => 1250, 'cost_price' => 950, 'stock' => 14, 'min_stock_level' => 5,
                'is_trending' => true, 'is_bestseller' => false, 'is_new' => true, 'is_featured' => true,
                'rating' => 4.5, 'review_count' => 19,
                'short_description' => 'A widely used undergraduate economics textbook.',
                'description' => 'Covers microeconomics and macroeconomics fundamentals for undergraduate business and economics students.',
            ],

            // ── CHILDREN BOOKS ───────────────────────────────────
            [
                'category_id' => $children, 'author_id' => $authorId('Roald Dahl'), 'publisher_id' => $publisherId('Penguin Random House'),
                'name' => 'Charlie and the Chocolate Factory', 'isbn' => '9780142410318',
                'price' => 550, 'sale_price' => null, 'cost_price' => 340, 'stock' => 45, 'min_stock_level' => 10,
                'is_trending' => true, 'is_bestseller' => true, 'is_new' => false, 'is_featured' => true,
                'rating' => 4.9, 'review_count' => 156,
                'short_description' => 'The magical story of Charlie Bucket and Willy Wonka\'s factory.',
                'description' => 'Roald Dahl\'s beloved children\'s classic, full of whimsy and unforgettable characters.',
            ],
            [
                'category_id' => $children, 'author_id' => $authorId('Enid Blyton'), 'publisher_id' => $publisherId('HarperCollins'),
                'name' => 'The Famous Five: Five on a Treasure Island', 'isbn' => '9780340894586',
                'price' => 480, 'sale_price' => null, 'cost_price' => 300, 'stock' => 38, 'min_stock_level' => 10,
                'is_trending' => false, 'is_bestseller' => true, 'is_new' => false, 'is_featured' => false,
                'rating' => 4.6, 'review_count' => 88,
                'short_description' => 'The first adventure of the Famous Five.',
                'description' => 'Julian, Dick, Anne, George, and Timmy the dog set off on a treasure-hunting adventure.',
            ],

            // ── GENERAL BOOKS ─────────────────────────────────────
            [
                'category_id' => $general, 'author_id' => $authorId('Allama Muhammad Iqbal'), 'publisher_id' => $publisherId('Sang-e-Meel Publications'),
                'name' => 'Bang-e-Dra', 'isbn' => '9789693512402',
                'price' => 420, 'sale_price' => null, 'cost_price' => 260, 'stock' => 24, 'min_stock_level' => 6,
                'is_trending' => false, 'is_bestseller' => false, 'is_new' => false, 'is_featured' => true,
                'rating' => 4.9, 'review_count' => 47,
                'short_description' => 'Allama Iqbal\'s first Urdu poetry collection.',
                'description' => 'A landmark collection of Urdu poetry by the poet-philosopher Allama Muhammad Iqbal.',
            ],

            // ── STATIONERY ────────────────────────────────────────
            [
                'category_id' => $stationery, 'author_id' => null, 'publisher_id' => null,
                'name' => 'Ball Point Pen (Pack of 10)', 'isbn' => null,
                'price' => 250, 'sale_price' => null, 'cost_price' => 150, 'stock' => 200, 'min_stock_level' => 30,
                'is_trending' => false, 'is_bestseller' => true, 'is_new' => false, 'is_featured' => false,
                'rating' => 4.4, 'review_count' => 12,
                'short_description' => 'Smooth-writing ball point pens, pack of 10.',
                'description' => 'A pack of 10 reliable, smooth-writing ball point pens for everyday use.',
            ],
            [
                'category_id' => $stationery, 'author_id' => null, 'publisher_id' => null,
                'name' => 'Spiral Notebook A4 (200 Pages)', 'isbn' => null,
                'price' => 180, 'sale_price' => null, 'cost_price' => 100, 'stock' => 150, 'min_stock_level' => 25,
                'is_trending' => false, 'is_bestseller' => false, 'is_new' => false, 'is_featured' => false,
                'rating' => 4.3, 'review_count' => 9,
                'short_description' => 'A4 spiral-bound notebook, 200 ruled pages.',
                'description' => 'Durable A4 spiral notebook with 200 ruled pages, ideal for school and office use.',
            ],

            // ── OFFICE SUPPLIES ───────────────────────────────────
            [
                'category_id' => $office, 'author_id' => null, 'publisher_id' => null,
                'name' => 'Stapler with 1000 Pins', 'isbn' => null,
                'price' => 320, 'sale_price' => 280, 'cost_price' => 190, 'stock' => 40, 'min_stock_level' => 8,
                'is_trending' => false, 'is_bestseller' => false, 'is_new' => false, 'is_featured' => false,
                'rating' => 4.2, 'review_count' => 6,
                'short_description' => 'Standard office stapler with 1000 pins included.',
                'description' => 'A durable, easy-grip stapler bundled with 1000 pins — an office essential.',
            ],
            [
                'category_id' => $office, 'author_id' => null, 'publisher_id' => null,
                'name' => 'A4 Printing Paper Ream (500 Sheets)', 'isbn' => null,
                'price' => 950, 'sale_price' => null, 'cost_price' => 700, 'stock' => 3, 'min_stock_level' => 10,
                'is_trending' => false, 'is_bestseller' => false, 'is_new' => false, 'is_featured' => false,
                'rating' => 4.5, 'review_count' => 14,
                'short_description' => '500-sheet ream of high-quality A4 printing paper.',
                'description' => 'Bright, smooth A4 paper suitable for printing, copying, and everyday office use.',
            ],
        ];

        foreach ($products as $data) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
            $data['sku']  = 'BKD-' . strtoupper(Str::random(8));
            Product::create($data);
        }
    }
}
