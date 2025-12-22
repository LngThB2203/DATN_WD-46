<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kiểm tra nếu đã có dữ liệu rồi thì không insert thêm
        if (DB::table('roles')->count() > 0) {
            return;
        }

        // Tạo các role
        DB::table('roles')->insert([
            [
                'role_name'   => 'Admin',
                'description' => 'Quản trị viên hệ thống',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'role_name'   => 'User',
                'description' => 'Khách hàng thường',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
