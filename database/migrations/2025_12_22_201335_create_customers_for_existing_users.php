<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tạo customer records cho users có role = 'user' nhưng chưa có customer record
        $users = DB::table('users')
            ->leftJoin('customers', 'users.id', '=', 'customers.user_id')
            ->where('users.role', 'user')
            ->whereNull('customers.id')
            ->select('users.id')
            ->get();

        foreach ($users as $user) {
            DB::table('customers')->insert([
                'user_id'          => $user->id,
                'membership_level' => 'Silver',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa customer records được tạo bởi migration này
        DB::table('customers')
            ->whereIn('user_id', DB::table('users')->where('role', 'user')->pluck('id'))
            ->delete();
    }
};
