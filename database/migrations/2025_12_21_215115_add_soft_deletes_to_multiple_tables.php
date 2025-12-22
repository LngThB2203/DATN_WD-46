<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Newsletter
        Schema::table('newsletters', function (Blueprint $table) {
            if (!Schema::hasColumn('newsletters', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Contacts
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Warehouse
        Schema::table('warehouse', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouse', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Discounts
        Schema::table('discounts', function (Blueprint $table) {
            if (!Schema::hasColumn('discounts', 'deleted_at')) {
                $table->softDeletes();
    }
        });

        // Reviews
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Posts
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Banners
        Schema::table('banners', function (Blueprint $table) {
            if (!Schema::hasColumn('banners', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Customers
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Product Galleries
        Schema::table('product_galleries', function (Blueprint $table) {
            if (!Schema::hasColumn('product_galleries', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('warehouse', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('product_galleries', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
