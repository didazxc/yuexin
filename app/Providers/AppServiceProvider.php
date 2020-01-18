<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::before(function (JobProcessing $event) {
            //创建运行标识文件
            if($event->job->getQueue()=='yx' && $event->job->resolveName()== 'App\Jobs\ProjectShell'){
                $cmd= unserialize($event->job->payload()['data']['command']);
                $cmd->before();
            }
        });

        Queue::after(function (JobProcessed $event) {
            //创建结束标识文件
            if($event->job->getQueue()=='yx' && $event->job->resolveName()== 'App\Jobs\ProjectShell'){
                $cmd = unserialize($event->job->payload()['data']['command']);
                $cmd->after();
            }
        });

        Queue::failing(function (JobFailed $event) {
            if($event->job->getQueue()=='yx' && $event->job->resolveName()== 'App\Jobs\ProjectShell'){
                $cmd = unserialize($event->job->payload()['data']['command']);
                $cmd->failing();
            }
        });

    }
}
