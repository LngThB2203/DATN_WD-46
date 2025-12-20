<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {

            $table->unsignedBigInteger('scent_id')->nullable()->after('size_id');
            $table->unsignedBigInteger('concentration_id')->nullable()->after('scent_id');
            $table->string('gender', 20)->nullable()->after('price_adjustment');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn([
                'scent_id',
                'concentration_id',
                'gender',
            ]);
        });
    }
};
