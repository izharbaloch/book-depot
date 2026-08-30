<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->after('category_id')->constrained('authors')->nullOnDelete();
            $table->foreignId('publisher_id')->nullable()->after('author_id')->constrained('publishers')->nullOnDelete();
            $table->string('isbn')->nullable()->unique()->after('sku');
            $table->string('barcode')->nullable()->unique()->after('isbn');
            $table->decimal('cost_price', 10, 2)->default(0)->after('sale_price');
            $table->integer('min_stock_level')->default(5)->after('stock');
            $table->decimal('weight', 8, 2)->nullable()->after('min_stock_level');

            $table->dropColumn(['sizes', 'colors', 'material', 'care_instructions']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('author_id');
            $table->dropConstrainedForeignId('publisher_id');
            $table->dropColumn(['isbn', 'barcode', 'cost_price', 'min_stock_level', 'weight']);

            $table->json('sizes')->nullable();
            $table->json('colors')->nullable();
            $table->string('material')->nullable();
            $table->string('care_instructions')->nullable();
        });
    }
};
