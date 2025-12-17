<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('stock_transactions');
    }

    public function down(): void
    {
        // không rollback bảng cũ
    }
};
