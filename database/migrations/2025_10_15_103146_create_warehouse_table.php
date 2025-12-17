<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse', function (Blueprint $table) {
            $table->id(); // warehouse_id
            $table->string('warehouse_name', 200);
            $table->string('address')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable(); // manager -> users
            $table->string('phone')->nullable();
            $table->timestamps();

            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse');
    }
};
