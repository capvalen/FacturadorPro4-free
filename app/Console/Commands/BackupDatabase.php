<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Ifsnop\Mysqldump as IMysqldump;
use Exception;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bk:bd --type={type} --database={database?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database';

    protected $process;

    protected $host;
    protected $username;
    protected $password;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $type = $this->argument('type');
            $database = $this->argument('database');
            $this->initDbConfig();

			if ($type === 'individual') {
                if (!is_dir(storage_path('app/backups/' . $database))) mkdir(storage_path('app/backups/' . $database));

				$tenant_dump = new IMysqldump\Mysqldump(
                    'mysql:host=' . $this->host . ';dbname=' . $database, $this->username, $this->password
                );
				$tenant_dump->start(storage_path("app/backups/{$database}/{$database}.sql"));
                Log::info('Backup ' . $database . ' database success');
			} else {
                $today = now()->format('dmY');
                if (!is_dir(storage_path('app/backups'))) mkdir(storage_path('app/backups'));
                if (!is_dir(storage_path('app/backups/'.$today))) mkdir(storage_path('app/backups/'.$today));

				$dbs = DB::table('websites')->get()->toArray();
				$db_admin = config('database.connections.mysql.database');

				foreach ($dbs as $db) {
					$tenant_dump = new IMysqldump\Mysqldump('mysql:host=' . $this->host . ';dbname=' . $db->uuid, $this->username, $this->password);
					$tenant_dump->start(storage_path("app/backups/{$today}/{$db->uuid}.sql"));
				}

				$system_dump = new IMysqldump\Mysqldump('mysql:host=' . $this->host . ';dbname=' . $db_admin, $this->username, $this->password);
				$system_dump->start(storage_path("app/backups/{$today}/{$db_admin}.sql"));

				Log::info('Backup database success');
			}


        }catch (Exception $e) {

            Log::error("Backup failed -- Line: {$e->getLine()} - Message: {$e->getMessage()} - File: {$e->getFile()}");

            return [
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage()
            ];

        }

    }


    private function initDbConfig(){

        $dbConfig = config('database.connections.' . config('tenancy.db.system-connection-name', 'system'));

        $this->host = Arr::first(Arr::wrap($dbConfig['host'] ?? ''));
        $this->username = $dbConfig['username'];
        $this->password = $dbConfig['password'];

    }

}
