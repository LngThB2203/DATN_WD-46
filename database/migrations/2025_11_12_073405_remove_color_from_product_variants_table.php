<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_variants', function (Blueprint $table) {

            if (Schema::hasColumn('product_variants', 'color_id')) {
                $table->dropForeign(['color_id']);
                $table->dropColumn('color_id');
            }

            if (! Schema::hasColumn('product_variants', 'size_id')) {
                $table->foreignId('size_id')
                    ->nullable()
                    ->constrained('variants_sizes')
                    ->nullOnDelete()
                    ->after('product_id');
            }
        });
    }

    public function down()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreignId('color_id')
                ->nullable()
                ->constrained('variants_colors')
                ->nullOnDelete()
                ->after('product_id');
        });
    }
};
