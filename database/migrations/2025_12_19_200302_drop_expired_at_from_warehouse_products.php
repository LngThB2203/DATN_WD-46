<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_products', function (Blueprint $table) {
            if (Schema::hasColumn('warehouse_products', 'expired_at')) {
                $table->dropColumn('expired_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->date('expired_at')->nullable();
        });
    }
};
