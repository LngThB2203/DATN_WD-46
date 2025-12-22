<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('stock_transactions', 'type')) {
            Schema::table('stock_transactions', function (Blueprint $table) {
                $table->enum('type', ['import', 'sale', 'cancel', 'adjust', 'export'])->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stock_transactions', 'type')) {
            Schema::table('stock_transactions', function (Blueprint $table) {
                $table->enum('type', ['import', 'sale', 'cancel', 'adjust'])->change();
            });
        }
    }
};
