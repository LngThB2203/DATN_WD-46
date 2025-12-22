<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stock_transactions')) {
            Schema::create('stock_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('warehouse_id')->default(1);
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('variant_id');
                $table->string('batch_code', 100);
                $table->enum('type', ['import', 'sale', 'cancel', 'adjust', 'export'])->default('import');
                $table->integer('quantity');
                $table->integer('before_quantity');
                $table->integer('after_quantity');
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->timestamps();
            });
            return;
        }

        $hasWarehouseId = Schema::hasColumn('stock_transactions', 'warehouse_id');
        $hasProductId = Schema::hasColumn('stock_transactions', 'product_id');
        $hasVariantId = Schema::hasColumn('stock_transactions', 'variant_id');
        $hasBatchCode = Schema::hasColumn('stock_transactions', 'batch_code');
        $hasType = Schema::hasColumn('stock_transactions', 'type');
        $hasQuantity = Schema::hasColumn('stock_transactions', 'quantity');
        $hasBeforeQuantity = Schema::hasColumn('stock_transactions', 'before_quantity');
        $hasAfterQuantity = Schema::hasColumn('stock_transactions', 'after_quantity');
        $hasReferenceType = Schema::hasColumn('stock_transactions', 'reference_type');
        $hasReferenceId = Schema::hasColumn('stock_transactions', 'reference_id');
        $hasCreatedAt = Schema::hasColumn('stock_transactions', 'created_at');
        $hasUpdatedAt = Schema::hasColumn('stock_transactions', 'updated_at');

        Schema::table('stock_transactions', function (Blueprint $table) use (
            $hasWarehouseId,
            $hasProductId,
            $hasVariantId,
            $hasBatchCode,
            $hasType,
            $hasQuantity,
            $hasBeforeQuantity,
            $hasAfterQuantity,
            $hasReferenceType,
            $hasReferenceId,
            $hasCreatedAt,
            $hasUpdatedAt
        ) {
            if (! $hasWarehouseId) {
                $table->unsignedBigInteger('warehouse_id')->default(1);
            }
            if (! $hasProductId) {
                $table->unsignedBigInteger('product_id');
            }
            if (! $hasVariantId) {
                $table->unsignedBigInteger('variant_id')->default(0);
            }
            if (! $hasBatchCode) {
                $table->string('batch_code', 100)->default('');
            }
            if (! $hasType) {
                $table->enum('type', ['import', 'sale', 'cancel', 'adjust', 'export'])->default('import');
            }
            if (! $hasQuantity) {
                $table->integer('quantity')->default(0);
            }
            if (! $hasBeforeQuantity) {
                $table->integer('before_quantity')->default(0);
            }
            if (! $hasAfterQuantity) {
                $table->integer('after_quantity')->default(0);
            }
            if (! $hasReferenceType) {
                $table->string('reference_type')->nullable();
            }
            if (! $hasReferenceId) {
                $table->unsignedBigInteger('reference_id')->nullable();
            }
            if (! $hasCreatedAt) {
                $table->timestamp('created_at')->nullable();
            }
            if (! $hasUpdatedAt) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
