<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('customers', function (Blueprint $table) {
        $table->enum('gender', ['Nam', 'Nữ', 'Khác'])->nullable()->after('address');
        $table->string('membership_level')->default('Silver')->after('gender');
        $table->boolean('status')->default(1)->after('membership_level');
    });
}

public function down()
{
    Schema::table('customers', function (Blueprint $table) {
        $table->dropColumn(['gender', 'membership_level', 'status']);
    });
}
};
