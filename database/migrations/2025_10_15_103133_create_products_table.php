<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // product_id
            $table->string('name', 200);
            $table->string('sku', 100)->nullable()->unique();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('slug', 200)->nullable()->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
