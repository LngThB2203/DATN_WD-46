<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // order_id
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable(); // optional link to payments
            $table->string('order_status', 50)->default('pending');
            $table->decimal('total_price', 12, 2)->default(0);
            $table->text('shipping_address')->nullable();
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('set null');
            // payment_id will be set from payments table if needed; can keep nullable FK to payments if desired
            // we avoid circular FK by not referencing payments here; payments will reference orders.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
