<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $now = now();

        DB::table('settings')->insert([
            ['key' => 'app_name', 'value' => 'Task App', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_short_name', 'value' => 'Task App', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_subtitle', 'value' => 'Management Project', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_description', 'value' => 'Aplikasi manajemen tugas Sprint35', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_motto', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_address', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_phone', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_email', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_email_domain', 'value' => 'sprint35.id', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_social_facebook', 'value' => '#', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_social_instagram', 'value' => '#', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_social_youtube', 'value' => '#', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_social_tiktok', 'value' => '#', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $domain = DB::table('settings')->where('key', 'app_email_domain')->value('value') ?? 'sprint35.id';

        DB::table('users')->insert([
            [
                'name' => 'Super Administrator',
                'username' => 'super-admin',
                'email' => 'superadmin@' . $domain,
                'role' => 'super-admin',
                'password' => bcrypt('password123'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Admin 1',
                'username' => 'admin-1',
                'email' => 'admin1@' . $domain,
                'role' => 'admin',
                'password' => bcrypt('password123'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Admin 2',
                'username' => 'admin-2',
                'email' => 'admin2@' . $domain,
                'role' => 'admin',
                'password' => bcrypt('password123'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
