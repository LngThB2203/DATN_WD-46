<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Kiểm tra index tồn tại rồi mới drop để tránh lỗi khi migrate nhiều lần
        $indexes = DB::select("SHOW INDEX FROM `warehouse_products` WHERE Key_name = 'unique_stock'");

        if (!empty($indexes)) {
            DB::statement('ALTER TABLE `warehouse_products` DROP INDEX `unique_stock`');
        }
    }

    public function down(): void
    {
        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->unique(
                ['warehouse_id', 'variant_id', 'batch_code'],
                'unique_stock'
            );
        });
    }

};
