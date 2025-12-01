<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id(); // discount_id
            $table->string('code', 100)->nullable();
            $table->string('discount_type', 50)->nullable(); // percent/fixed
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('min_order_value', 10, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
