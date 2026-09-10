<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('username', 'super-admin')->first();
        $admin1 = User::where('username', 'admin-1')->first();
        $admin2 = User::where('username', 'admin-2')->first();

        $tasks = [
            [
                'user' => $superAdmin,
                'title' => 'Menyusun laporan bulanan',
                'description' => 'Rangkum capaian kerja bulan ini beserta lampiran data pendukung.',
                'category_id' => 1,
                'priority' => Task::PRIORITY_TINGGI,
                'status' => Task::STATUS_DIKERJAKAN,
                'due_date' => now()->addDays(2)->toDateString(),
            ],
            [
                'user' => $superAdmin,
                'title' => 'Review proposal vendor',
                'description' => 'Cek kelengkapan dokumen dan bandingkan penawaran harga.',
                'category_id' => 1,
                'priority' => Task::PRIORITY_SEDANG,
                'status' => Task::STATUS_BELUM,
                'due_date' => now()->addDays(6)->toDateString(),
            ],
            [
                'user' => $superAdmin,
                'title' => 'Backup data server',
                'description' => null,
                'category_id' => 5,
                'priority' => Task::PRIORITY_TINGGI,
                'status' => Task::STATUS_BELUM,
                'due_date' => now()->subDays(3)->toDateString(),
            ],
            [
                'user' => $superAdmin,
                'title' => 'Menyiapkan materi briefing',
                'description' => 'Slide ringkas untuk briefing tim awal pekan.',
                'category_id' => 4,
                'priority' => Task::PRIORITY_SEDANG,
                'status' => Task::STATUS_SELESAI,
                'due_date' => now()->subDays(1)->toDateString(),
            ],
            [
                'user' => $superAdmin,
                'title' => 'Belajar Laravel Livewire',
                'description' => 'Selesaikan modul komponen dan event.',
                'category_id' => 3,
                'priority' => Task::PRIORITY_RENDAH,
                'status' => Task::STATUS_BELUM,
                'due_date' => now()->addDays(12)->toDateString(),
            ],
            [
                'user' => $admin1,
                'title' => 'Update data pelanggan',
                'description' => 'Perbarui kontak dan alamat pelanggan aktif.',
                'category_id' => 1,
                'priority' => Task::PRIORITY_TINGGI,
                'status' => Task::STATUS_DIKERJAKAN,
                'due_date' => now()->toDateString(),
            ],
            [
                'user' => $admin1,
                'title' => 'Menjawab email klien',
                'description' => 'Balas pertanyaan estimasi biaya dari dua klien.',
                'category_id' => 1,
                'priority' => Task::PRIORITY_SEDANG,
                'status' => Task::STATUS_BELUM,
                'due_date' => now()->addDays(1)->toDateString(),
            ],
            [
                'user' => $admin1,
                'title' => 'Rapikan arsip dokumen',
                'description' => null,
                'category_id' => 5,
                'priority' => Task::PRIORITY_RENDAH,
                'status' => Task::STATUS_SELESAI,
                'due_date' => now()->subDays(5)->toDateString(),
            ],
            [
                'user' => $admin1,
                'title' => 'Membuat konsep konten medsos',
                'description' => 'Draft tiga konten untuk pekan depan.',
                'category_id' => 2,
                'priority' => Task::PRIORITY_SEDANG,
                'status' => Task::STATUS_BELUM,
                'due_date' => now()->subDays(2)->toDateString(),
            ],
            [
                'user' => $admin2,
                'title' => 'Menyiapkan notulen rapat',
                'description' => 'Rapat koordinasi mingguan bersama seluruh divisi.',
                'category_id' => 4,
                'priority' => Task::PRIORITY_TINGGI,
                'status' => Task::STATUS_DIKERJAKAN,
                'due_date' => now()->addDays(3)->toDateString(),
            ],
            [
                'user' => $admin2,
                'title' => 'Cek perangkat kantor',
                'description' => 'Inventaris kondisi printer dan komputer.',
                'category_id' => 5,
                'priority' => Task::PRIORITY_RENDAH,
                'status' => Task::STATUS_BELUM,
                'due_date' => now()->addDays(9)->toDateString(),
            ],
            [
                'user' => $admin2,
                'title' => 'Kursus online manajemen waktu',
                'description' => 'Selesaikan dua modul pertama.',
                'category_id' => 3,
                'priority' => Task::PRIORITY_SEDANG,
                'status' => Task::STATUS_DIKERJAKAN,
                'due_date' => null,
            ],
        ];

        foreach ($tasks as $task) {
            $user = $task['user'];
            unset($task['user']);

            $user->tasks()->create($task);
        }
    }
}
