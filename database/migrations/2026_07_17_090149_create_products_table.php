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
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name_en');
            $table->string('name_bn');
            $table->string('scientific_name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->integer('stock_qty');
            $table->text('description_en');
            $table->text('description_bn');
            $table->enum('care_level', ['Easy', 'Medium', 'Hard']);
            $table->integer('tank_size_liters')->nullable();
            $table->integer('weight_grams');
            $table->string('image_path');
            $table->boolean('is_featured')->default(false);
            $table->decimal('avg_rating', 2, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->timestamps();

            $table->index('category_id');
            $table->index('slug');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
