<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->string('batch_code', 100)->nullable()->after('variant_id');
            $table->date('expired_at')->nullable()->after('batch_code');
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->dropColumn(['batch_code', 'expired_at']);
        });
    }
};
