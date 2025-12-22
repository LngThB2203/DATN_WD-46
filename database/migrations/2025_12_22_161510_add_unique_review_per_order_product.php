<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $indexName = 'reviews_user_order_product_unique';

            if (Schema::hasColumn('reviews', 'user_id') && Schema::hasColumn('reviews', 'order_id') && Schema::hasColumn('reviews', 'product_id')) {
                $table->unique(['user_id', 'order_id', 'product_id'], $indexName);
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $indexName = 'reviews_user_order_product_unique';
            $table->dropUnique($indexName);
        });
    }
};
