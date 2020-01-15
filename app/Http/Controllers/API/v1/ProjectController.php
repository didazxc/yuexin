<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\Controller;
use App\Http\Services\Image;
use App\Http\Services\ProjectFile;
use App\Models\Project;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
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
        $raw_root=$request->input('importProjectDir','');
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
        $files = ProjectFile::imgFiles($project_dir,"Movies");
        //对img作缓存
        //整合MotionCor和CTF模块
        $res=$files->map(function($it)use($project_dir){
            $name=$it['name'];
            $item=[];
            $item['name']=$name;
            $item['Movies']=$it['path'];
            $item['MotionCor']=ProjectFile::existPng($project_dir,'MotionCor',$name,'mrc');
            $item['CTF']=ProjectFile::existPng($project_dir,'CTF',$name,'ctf');
            $item['Mark']='good';
            $item['Pick']=1000;
            $item['Extract']=ProjectFile::existPng($project_dir,'CTF',$name,'ctf');
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
        $request->validate(['projectDir'=>'required','name'=>'required']);
        $project_dir=$request->input('projectDir');
        $name=$request->input("name");
        try {
            $cmd = ProjectFile::getTestCmd($project_dir, $name);
        } catch (FileNotFoundException $e) {
            abort(405,"找不到命令配置文件");
        }
        return $this->response(trim(shell_exec("$cmd 2>&1")));
    }

    public function preprocess(Request $request){
        $request->validate(['projectDir'=>'required']);
        $project_dir=$request->input('projectDir');
        $files = ProjectFile::imgFiles($project_dir,"Movies");
        $res=$files->map(function($it)use($project_dir){
            $name=$it['name'];
            $item=[];
            $item['name']=$name;
            $item['df']=10;
            $item['fit']=50;
            $item['astig']=100;
            $item['mark']='good';

            $item['src']=[];
            $item['src']['Movies']=$project_dir.'/Movies/'.$name.'.mrc';
            $item['src']['MotionCor']=$project_dir.'/MotionCor/'.$name.'.mrc';
            $item['src']['CTF']=$project_dir.'/CTF/'.$name.'.ctf';

            return $item;
        })->values()->toArray();
        return $this->response($res);
    }

    public function pick(Request $request){
        $request->validate(['projectDir'=>'required']);
        $project_dir=$request->input('projectDir');
        $files = ProjectFile::imgFiles($project_dir,"Movies");
        $res=$files->map(function($it)use($project_dir){
            $name=$it['name'];
            $item=[];
            $item['name']=$name;
            $item['df']=10;
            $item['fit']=50;
            $item['astig']=100;
            $item['mark']='good';
            $item['picks']=200;

            $item['src']=$project_dir.'/MotionCor/'.$name.'.mrc';

            return $item;
        });
        return $this->response($res);
    }

    public function getMark(Request $request){
        $request->validate(['projectDir'=>'required','name'=>'required']);
        $project_dir=$request->input('projectDir');
        $name=$request->input("name");
        $res = ProjectFile::getStar($project_dir.'/Mark/'.$name.'_automatch.star');
        return $this->response($res);
    }

    public function setMark(Request $request){
        $request->validate(['projectDir'=>'required','name'=>'required','arr'=>'required|array']);
        $project_dir=$request->input('projectDir');
        $name=$request->input("name");
        $arr=$request->input("arr");
        ProjectFile::saveStar($project_dir.'/Mark/'.$name.'.star',$arr);
        return $this->response('done');
    }

}