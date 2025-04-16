<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

//Basic command to create a psql/mysql database for convenience on first setup
//Uses db name from env file
class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = config('database.default');
        $config = config("database.connections.$connection");
        $dbName = $config['database'];
        $driver = $config['driver'];

        // Temporarily connect without selecting a database
        config(["database.connections.$connection.database" => null]);
        DB::purge($connection);
        DB::reconnect($connection);

        try {
            if ($driver === 'mysql') {
                DB::statement("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } elseif ($driver === 'pgsql') {
                $exists = DB::select("SELECT 1 FROM pg_database WHERE datname = ?", [$dbName]);
                if (empty($exists)) {
                    DB::statement("CREATE DATABASE \"$dbName\"");
                }
            } else {
                $this->error("Unsupported driver: $driver");
                return 1;
            }

            $this->info("SUCCESS: Database '$dbName' created or already exists.");
        } catch (\Exception $e) {
            $this->error("Error creating database: " . $e->getMessage() . ", create it manually then update dev if issue persists.");
            return 1;
        }

        return 0;
    }
}
