<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('category_id')->constrained('product_categories')->nullOnDelete();
            $table->decimal('price', 12, 2);
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('reorder_point')->default(10);
            $table->string('barcode')->nullable()->unique();
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};