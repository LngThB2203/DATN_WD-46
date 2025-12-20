<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed a default admin role and user account.
     */
    public function run(): void
    {
        $roleId = DB::table('roles')->where('role_name', 'Admin')->value('id');

        if (! $roleId) {
            $roleId = DB::table('roles')->insertGetId([
                'role_name' => 'Admin',
                'description' => 'Default system administrator role',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin@123'),
            ]
        );

        $admin->role_id = $roleId;
        $admin->email_verified_at = now();
        $admin->save();
    }
}
