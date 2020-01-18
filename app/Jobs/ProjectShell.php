<?php

namespace App\Jobs;

use App\Http\Services\Task;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProjectShell implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    //public $timeout = 120;

    public $cmd;
    public $project_dir;
    public $modules;
    public $module;
    public $name;
    public $return_status;

    public function __construct(string $project_dir,array $modules,string $module,string $name,string $cmd)
    {
        $this->project_dir=$project_dir;
        $this->modules=$modules;
        $this->module=$module;
        $this->name=$name;
        $this->cmd=$cmd;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        //运行命令
        $res='';
        exec($this->cmd,$res,$this->return_status);
        if($this->return_status){
            throw new Exception(implode("\n",$res));
        }
    }

    public function before(){
        Task::setTaskStatus($this->project_dir,$this->module,$this->name,Task::RUNNING);
    }

    public function after(){
        Task::setTaskStatus($this->project_dir,$this->module,$this->name,Task::FINISHED);
    }

    public function failing(){
        $k=array_search($this->module,$this->modules);
        foreach(array_slice($this->modules,$k) as $mod){
            Task::setTaskStatus($this->project_dir, $mod, $this->name, Task::FAILED);
        }
    }

}

