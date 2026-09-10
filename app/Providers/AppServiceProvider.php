<?php

namespace App\Providers;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private const DATABASE_COMMANDS = [
        'migrate',
        'migrate:fresh',
        'migrate:install',
        'migrate:refresh',
        'migrate:rollback',
        'db:wipe',
        'db:seed',
        'db:show',
        'db:table',
        'schema:dump',
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->listenDatabaseCommands();
    }

    /**
     * Buat database MySQL otomatis saat perintah migrasi pertama kali
     * dijalankan di lingkungan baru, selama user database memiliki
     * hak CREATE.
     */
    private function listenDatabaseCommands(): void
    {
        Event::listen(CommandStarting::class, function (CommandStarting $event) {
            if (! is_string($event->command) || ! in_array($event->command, self::DATABASE_COMMANDS, true)) {
                return;
            }

            $name = config('database.default');
            $config = config("database.connections.{$name}");

            if (! in_array(($config['driver'] ?? ''), ['mysql', 'mariadb'], true)) {
                return;
            }

            $database = $config['database'] ?? null;
            if (empty($database)) {
                return;
            }

            $config['database'] = null;
            config(['database.connections.database-provision' => $config]);

            try {
                $provision = DB::connection('database-provision');

                $exists = $provision->selectOne(
                    'SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?',
                    [$database]
                );

                if ($exists === null) {
                    $safeName = str_replace('`', '', $database);
                    $charset = $config['charset'] ?? 'utf8mb4';
                    $collation = $config['collation'] ?? 'utf8mb4_unicode_ci';

                    $provision->statement(
                        "CREATE DATABASE `{$safeName}` CHARACTER SET {$charset} COLLATE {$collation}"
                    );

                    DB::purge($name);
                }
            } catch (\Throwable) {
                // Server tidak terjangkau atau kredensial salah — biarkan
                // pesan error asli dari perintah migrasi yang muncul.
            } finally {
                DB::purge('database-provision');
            }
        });
    }
}
