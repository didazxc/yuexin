<?php


namespace App\Http\Services;

use App\Jobs\ProjectShell;
use App\Jobs\Shell;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class Task
{
    //status
    const QUEUE = 'queue';
    const RUNNING='running';
    const FINISHED='finished';
    const RETRY='retry';
    const FAILED='failed';
    const NONE='none';

    const TaskStatus=[self::QUEUE,self::RUNNING,self::FINISHED,self::RETRY,self::FAILED];
    const TaskStatusTitle=[
        self::QUEUE=>'排队执行...',
        self::RUNNING=>'运行中...',
        self::FINISHED=>'执行完毕',
        self::RETRY=>'排队重试...',
        self::FAILED=>'失败'
    ];

    static public function getSingleTaskStatus($project_dir,$module,$name){
        foreach(self::TaskStatus as $s){
            $file=$project_dir.ProjectFile::ConfTaskDir."/$module/${s}_$name";
            if(Storage::disk(ProjectFile::Disk)->exists($file)){
                return $s;
            }
        }
        return self::NONE;
    }
    static public function setTaskStatus($project_dir,$module,$name,$status){
        $disk=Storage::disk(ProjectFile::Disk);
        if(!$disk->exists($project_dir.ProjectFile::ConfTaskDir)){
            $disk->makeDirectory($project_dir.ProjectFile::ConfTaskDir);
            $disk->makeDirectory($project_dir.ProjectFile::ConfTaskDir."/$module");
        }else if(!$disk->exists($project_dir.ProjectFile::ConfTaskDir."/$module")){
            $disk->makeDirectory($project_dir.ProjectFile::ConfTaskDir."/$module");
        }else{
            foreach(self::TaskStatus as $s){
                $file=$project_dir.ProjectFile::ConfTaskDir."/$module/${s}_$name";
                Storage::disk(ProjectFile::Disk)->delete($file);
            }
        }
        $tagfile=$project_dir.ProjectFile::ConfTaskDir."/$module/${status}_$name";
        $disk->put($tagfile,$status);
        return 'done';
    }
    static public function getTaskStatus($project_dir,$module){
        $dir=$project_dir.ProjectFile::ConfTaskDir.'/'.$module;
        $raw_list = Storage::disk(ProjectFile::Disk)->files($dir);
        $files=collect($raw_list)->map(function($item){
            $file_name=pathinfo($item,PATHINFO_BASENAME);
            $arr=explode('_',$file_name,2);
            return ['status'=>$arr[0],'name'=>$arr[1]];
        });
        return $files;
    }
    static public function getTaskStatusForView($project_dir,$module){
        $files=self::getTaskStatus($project_dir,$module)->map(function($item){
            $status=$item['status'];
            if(array_key_exists($status,self::TaskStatusTitle)){
                $status=self::TaskStatusTitle[$status];
            }
            return ['status'=>$status,'name'=>$item['name']];
        });
        return $files;
    }

    /**
     * 执行任务，默认只针对未执行过的movie
     * @param $project_dir string 项目目录
     * @param $modules array 模块
     * @param $name string mrc图片名称
     * @param $rerun bool 是否重新执行
     * @return string
     */
    static public function run(string $project_dir,array $modules,string $name,bool $rerun=false){
        //获取命令
        $cmds=[];
        try {
            foreach($modules as $mod){
                $cmd = ProjectFile::getCmd($project_dir,$mod,$name);
                $status=self::getSingleTaskStatus($project_dir,$mod,$name);
                if($status==Task::NONE || $rerun){
                    $cmds[] = new ProjectShell($project_dir,$modules,$mod,$name,$cmd);
                }
            }
        } catch (FileNotFoundException $e) {
            abort(405,"找不到命令配置文件");
        }
        //执行命令
        if(count($cmds)>0) {
            Shell::withChain($cmds)->dispatch('')->allOnQueue('yx');
            //标识文件
            foreach ($cmds as $cmd) {
                Task::setTaskStatus($cmd->project_dir, $cmd->module, $cmd->name, Task::QUEUE);
            }
        }
        return 'done';
    }

    static public function runAll($project_dir,array $modules){
        //todo:开启常驻进程，定时执行run，并增加停止在继续等功能
        $names = ProjectFile::imgFiles($project_dir,"Movies")->select("name");
        foreach($names as $name){
            self::run($project_dir,$modules,$name);
        }
    }

}