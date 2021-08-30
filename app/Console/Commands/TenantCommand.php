<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant\Task;
use Carbon\Carbon;
use Artisan;

class TenantCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:run';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute the scheduled tasks of the tenants';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        foreach (Task::where('execution_time', Carbon::now()->format('H:i').':00')->get() as $task) {
            try {
                Artisan::call($task->class);
                
                $task->output = Artisan::output();
                $task->save();
            }
            catch (\Exception $e) {
                $task->output = $e->getMessage();
                $task->save();
            }
        };
    }
}
