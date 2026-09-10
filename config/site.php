<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi utama aplikasi. Nilai-nilai ini diambil dari tabel settings
    | di database melalui SiteSettingServiceProvider. Khusus 'name' tetap
    | diambil dari .env (APP_NAME).
    |
    */

    'name' => env('APP_NAME', 'Laravel Starter'),
    'short_name' => 'Sprint35',
    'subtitle' => 'Task Management',
    'description' => 'Aplikasi manajemen tugas Sprint35',
    'motto' => '',

    'contact' => [
        'address' => '',
        'phone' => '',
        'email' => '',
    ],

    'social' => [
        'facebook' => '#',
        'instagram' => '#',
        'youtube' => '#',
        'tiktok' => '#',
    ],

    'admin' => [
        'default_email_domain' => 'sprint35.id',
    ],

];
