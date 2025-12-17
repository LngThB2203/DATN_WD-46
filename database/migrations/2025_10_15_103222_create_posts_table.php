<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); // post_id
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('title', 250);
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            $table->string('slug', 250)->nullable()->unique();
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
