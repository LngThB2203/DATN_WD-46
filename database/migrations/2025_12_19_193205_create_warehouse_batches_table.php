<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_batches', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();

            $table->string('batch_code', 100)->index();
            $table->date('expired_at')->nullable();

            $table->decimal('import_price', 12, 2)->default(0);
            $table->integer('quantity')->default(0);

            $table->timestamps();

            // FK
            $table->foreign('warehouse_id')
                ->references('id')->on('warehouse')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->cascadeOnDelete();

            $table->foreign('variant_id')
                ->references('id')->on('product_variants')
                ->nullOnDelete();
            
            // Indexes for better query performance
            $table->index(['warehouse_id', 'product_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_batches');
    }
};
