<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // user_id
            $table->string('name', 150);
            $table->string('avatar')->nullable();
            $table->string('address')->nullable();
            $table->string('email', 150)->unique();
            $table->string('phone', 50)->nullable();
            $table->string('password');
            $table->unsignedBigInteger('role_id')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
