<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id(); // variant_id
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('size_id')->nullable();
            $table->string('sku', 120)->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('price_adjustment', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('size_id')->references('id')->on('variants_sizes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
