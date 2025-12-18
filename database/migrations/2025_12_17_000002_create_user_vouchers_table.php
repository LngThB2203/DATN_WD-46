<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('discount_id')->constrained('discounts')->onDelete('cascade');
            $table->timestamp('saved_at')->useCurrent();
            $table->timestamp('used_at')->nullable();
            $table->unique(['user_id', 'discount_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_vouchers');
    }
};
