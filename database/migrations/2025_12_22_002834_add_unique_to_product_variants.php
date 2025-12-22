<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unique(
                ['product_id', 'size_id', 'scent_id', 'concentration_id', 'gender'],
                'unique_product_variant'
            );
        });
    }

    public function down()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique('unique_product_variant');
        });
    }
};
