<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->index('warehouse_id', 'idx_wp_warehouse_id');
            $table->index('variant_id', 'idx_wp_variant_id');
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->dropIndex('idx_wp_warehouse_id');
            $table->dropIndex('idx_wp_variant_id');
        });
    }

};
