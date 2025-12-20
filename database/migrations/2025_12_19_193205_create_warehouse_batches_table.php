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
            $table->unsignedBigInteger('variant_id');

            $table->string('batch_code', 100)->index();
            $table->date('expired_at')->nullable();

            $table->decimal('import_price', 12, 2);
            $table->integer('quantity');

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
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_batches');
    }
};
