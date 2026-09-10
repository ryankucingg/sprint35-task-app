<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('users')->where('role', 'super-admin')->doesntExist()) {
            return;
        }

        DB::table('users')->where('role', 'admin')->update(['role' => 'user']);
        DB::table('users')->where('role', 'super-admin')->update(['role' => 'admin']);

        DB::table('users')->where('username', 'super-admin')->update([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@sprint35.id',
        ]);
        DB::table('users')->where('username', 'admin-1')->update([
            'name' => 'Pengguna 1',
            'username' => 'user-1',
            'email' => 'user1@sprint35.id',
        ]);
        DB::table('users')->where('username', 'admin-2')->update([
            'name' => 'Pengguna 2',
            'username' => 'user-2',
            'email' => 'user2@sprint35.id',
        ]);
    }

    public function down(): void
    {
        if (DB::table('users')->where('role', 'super-admin')->exists()) {
            return;
        }

        DB::table('users')->where('role', 'admin')->update(['role' => 'super-admin']);
        DB::table('users')->where('role', 'user')->update(['role' => 'admin']);
    }
};
