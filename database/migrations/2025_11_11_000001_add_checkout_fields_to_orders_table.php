<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('user_id');
            $table->string('customer_email')->nullable()->after('customer_name');
            $table->string('customer_phone')->nullable()->after('customer_email');

            $table->string('shipping_province')->nullable()->after('shipping_address');
            $table->string('shipping_district')->nullable()->after('shipping_province');
            $table->string('shipping_ward')->nullable()->after('shipping_district');
            $table->string('shipping_address_line')->nullable()->after('shipping_ward');
            $table->text('customer_note')->nullable()->after('shipping_address_line');

            $table->decimal('subtotal', 12, 2)->default(0)->after('total_price');
            $table->decimal('discount_total', 12, 2)->default(0)->after('subtotal');
            $table->decimal('grand_total', 12, 2)->default(0)->after('discount_total');
            $table->string('payment_method', 50)->nullable()->after('grand_total');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name',
                'customer_email',
                'customer_phone',
                'shipping_province',
                'shipping_district',
                'shipping_ward',
                'shipping_address_line',
                'customer_note',
                'subtotal',
                'discount_total',
                'grand_total',
                'payment_method',
            ]);
        });
    }
};

