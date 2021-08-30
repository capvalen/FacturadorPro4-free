<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Requests\Tenant\TaskRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\Task;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Exclude
     * @var array
     */
    private $exclude = ['.', '..', 'TenantCommand.php'];
    
    /**
     * Path
     * @var string
     */
    private $path = "App\Console\Commands\\";
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('tenant.task.index');
    }
    
    /**
     * Tables
     * @return \Illuminate\Http\Response
     */
    public function tables() {
            return Task::all();
    }
    
    /**
     * Lists Command
     * @return \Illuminate\Http\Response
     */
    public function listsCommand() {
        return collect(array_diff(scandir(app_path('Console/Commands')), $this->exclude))->map(function($fileCommand) {
            $name = explode('.', $fileCommand)[0];
            
            return [
                'name' => $name,
                'class' => "{$this->path}{$name}"
            ];
        });
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Tenant\TaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request) {
        try {
            $task = Task::create([
                'class' => $request->class,
                'execution_time' => Carbon::parse($request->execution_time)->format('H:i:s'),
            ]);
            
            return [
                'success' => true,
                'message' => 'Se registró la tarea con éxito.'
            ];
        }
        catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task) {
        $task->delete();
        
        return [
            'success' => true,
            'message' => 'Se eliminó la tarea con éxito.'
        ];
    }
}
