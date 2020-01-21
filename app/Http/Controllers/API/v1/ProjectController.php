<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\Controller;
use App\Http\Services\Module;
use App\Http\Services\ProjectFile;
use App\Http\Services\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function create(Request $request){
        $request->validate(['projectForm'=>'required']);
        $form=$request->input('projectForm');
        $project_dir=$form['directory'];
        $project=Project::create([
            'user_id'=>$request->user()->id,
            'name'=>$form['name'],
            'directory'=>$project_dir,
            'ssd_directory'=>$project_dir,
            'args'=>$form['map'],
        ]);
        $raw_root=$form['importProjectDir'];
        ProjectFile::initConf($project_dir,$raw_root);
        return $this->response($project);
    }

    public function png(Request $request){
        $request->validate(['projectDir'=>'required','module'=>'required','name'=>'required','ext'=>'required']);
        $project_dir=$request->input('projectDir');
        $module=$request->input("module");
        $name=$request->input("name");
        $ext=$request->input("ext");
        $png=ProjectFile::png($project_dir,$module,$name,$ext);
        return $this->response($png);
    }

    public function clear(Request $request){
        $request->validate(['projectDir'=>'required']);
        $project_dir=$request->input('projectDir');
        ProjectFile::clear($project_dir);
        return $this->response('done');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function overview(Request $request){
        $request->validate(['projectDir'=>'required']);
        $project_dir=$request->input('projectDir');
        $files = ProjectFile::imgFiles($project_dir,'Movies');
        //运行任务
        $files->each(function($it)use($project_dir){
            //读取movies模块时，转换图片，每个任务将只运行一次
            Task::run($project_dir,['Movies'],$it['name']);
            //重新执行条件:star.png不存在或创建时间早于star文件
            $starFilePath = "$project_dir/Pick/${it['name']}.star";
            $rerun=ProjectFile::laterPng($starFilePath);
            if($rerun)Task::run($project_dir,['Extract'],$it['name'],$rerun);
        });
        //统一CTF中的star文件
        ProjectFile::combineCTFStarFiles($project_dir);
        //整理Pick中的star文件
        $star=ProjectFile::updateStarWithPicks($project_dir);
        //整合MotionCor,CTF,Extract和star文件
        $statusMovies=Task::getTaskStatusForView($project_dir,'Movies')->pluck('status','name');
        $statusMotionCor=Task::getTaskStatusForView($project_dir,'MotionCor')->pluck('status','name');
        $statusCTF=Task::getTaskStatusForView($project_dir,'CTF')->pluck('status','name');
        $statusExtract=Task::getTaskStatusForView($project_dir,'Extract')->pluck('status','name');
        $picks=[];
        foreach($star as $pick){
            $picks[$pick['name']]=$pick;
        }
        $res=$files->map(function($it)use($project_dir,$statusMovies,$statusMotionCor,$statusCTF,$statusExtract,$picks){
            $name=$it['name'];
            $item=[];
            $item['name']=$name;
            $item['Movies']=$statusMovies->get($name);//ProjectFile::existPng($project_dir,'Movies',$name,'mrc');
            $item['MotionCor']=$statusMotionCor->get($name);//ProjectFile::existPng($project_dir,'MotionCor',$name,'mrc');
            $item['CTF']=$statusCTF->get($name);//ProjectFile::existPng($project_dir,'CTF',$name,'ctf');
            $item['Mark']=array_key_exists($name,$picks)?$picks[$name]['mark']:'good';
            $item['Pick']=array_key_exists($name,$picks)?$picks[$name]['picks']:0;
            $item['Extract']=$statusExtract->get($name,'未开始');
            return $item;
        })->values()->toArray();
        return $this->response($res);
    }

    public function getConf(Request $request){
        $request->validate(['projectDir'=>'required']);
        $project_dir=$request->input('projectDir');
        ProjectFile::initConf($project_dir);
        return $this->response(ProjectFile::getCmds($project_dir));
    }

    public function setConf(Request $request){
        $request->validate(['projectDir'=>'required','conf'=>'required|array']);
        $project_dir=$request->input('projectDir');
        $conf_arr=$request->input('conf');
        ProjectFile::setCmds($project_dir,$conf_arr);
        return $this->response('done');
    }

    public function runTest(Request $request){
        $request->validate(['projectDir'=>'required','modules'=>'required|array','names'=>'required|array']);
        $project_dir=$request->input('projectDir');
        $names=$request->input("names");
        $modules=$request->input("modules");
        //判断顺序
        $m_len=count(Module::Modules);
        $index=0;
        foreach ($modules as $mod){
            for($i=$index;$i<$m_len;$i++){
                if(Module::Modules[$i]==$mod)break;
            }
            $index=$i;
        }
        if($index==$m_len){
            return $this->response('failed',503,'modules should been in order');
        }
        //执行命令
        //ProjectFile::clear($project_dir,$modules);
        foreach($names as $name){
            Task::run($project_dir,$modules,$name);
        }
        return $this->response('done');
    }

    public function runAll(Request $request){
        $request->validate(['projectDir'=>'required','modules'=>'required']);
        $project_dir=$request->input('projectDir');
        $modules=$request->input("modules");
        Task::runAll($project_dir,$modules);
    }

    public function preprocess(Request $request){
        $request->validate(['projectDir'=>'required']);
        $project_dir=$request->input('projectDir');
        ProjectFile::combineCTFStarFiles($project_dir);
        $star = ProjectFile::getStar("$project_dir/CTF/ctf.star");
        return $this->response($star);
    }

    public function setMark(Request $request){
        $request->validate(['projectDir'=>'required','marks'=>'required']);
        $project_dir=$request->input('projectDir');
        $marks=$request->input('marks');
        $marks=collect($marks)->pluck('mark','name')->toArray();
        $star_file="$project_dir/CTF/ctf.star";
        $star = ProjectFile::getStar($star_file);
        array_walk($star,function(&$item)use($marks){
            if(array_key_exists($item['name'],$marks)) $item['mark']=$marks[$item['name']];
            });
        ProjectFile::saveStar($star_file,$star);
        return $this->response('done');
    }

    public function pick(Request $request){
        $request->validate(['projectDir'=>'required']);
        $project_dir=$request->input('projectDir');
        $star=ProjectFile::updateStarWithPicks($project_dir);
        return $this->response($star);
    }

    public function getPick(Request $request){
        $request->validate(['projectDir'=>'required','name'=>'required']);
        $project_dir=$request->input('projectDir');
        $name=$request->input("name");
        $res = ProjectFile::getStar("$project_dir/Pick/$name.star");
        return $this->response($res);
    }

    public function setPick(Request $request){
        $request->validate(['projectDir'=>'required','name'=>'required','arr'=>'required|array']);
        $project_dir=$request->input('projectDir');
        $name=$request->input("name");
        $arr=$request->input("arr");
        ProjectFile::saveStar("$project_dir/Pick/$name.star",$arr);
        //更新star文件
        $needFreshStar=false;
        $picks=count($arr);
        $star = ProjectFile::getStar("$project_dir/CTF/ctf.star");
        array_walk($star,function(&$item)use($name,$picks,&$needFreshStar){
            if($item['name']==$name and $item['picks']!=$picks){
                $item['picks']=$picks;
                $needFreshStar=true;
            }
        });
        if($needFreshStar)ProjectFile::saveStar("$project_dir/CTF/ctf.star",$star);
        //----------
        return $this->response('done');
    }

}