<?php

namespace App\Jobs;

use App\Http\Services\Task;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Shell  implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $cmd;
    public $return_status;

    public function __construct(string $cmd)
    {
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
        if($this->cmd)
        exec($this->cmd,$res,$this->return_status);
    }
}
