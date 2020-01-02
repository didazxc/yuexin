<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Services\ProjectFile;
use App\Models\Project;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use App\Http\Services\Image;

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
        ProjectFile::initConf($project_dir);
        return $project;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection ['Movies','MotionCor','CTF','Mark','Pick','Extract']
     */
    public function overview(Request $request){
        $request->validate(['projectDir'=>'required']);
        $project_dir=$request->input('projectDir');
        $files = ProjectFile::movies($project_dir);
        //整合MotionCor和CTF模块
        $res=$files->map(function($it)use($project_dir){
            $name=$it['name'];
            $item=[];
            $item['name']=$name;
            $item['Movies']=$it['path'];
            $item['MotionCor']=$project_dir.'/MotionCor/'.$name.'.mrc';
            $item['CTF']=$project_dir.'/CTF/'.$name.'.ctf';
            $item['Mark']='good';
            $item['Pick']=1000;
            $item['Extract']=$project_dir.'/CTF/'.$name.'.ctf';
            return $item;
        });
        return $res;
    }

    public function mrc(Request $request){
        $request->validate(['path'=>'required']);
        $path=$request->input('path');
        return Image::mrc2png($path);
    }

    public function getConf(Request $request){
        $request->validate(['projectDir'=>'required']);
        $project_dir=$request->input('projectDir');
        ProjectFile::initConf($project_dir);
        return ProjectFile::getCmds($project_dir);
    }

    public function setConf(Request $request){
        $request->validate(['projectDir'=>'required','conf'=>'required|array']);
        $project_dir=$request->input('projectDir');
        $conf_arr=$request->input('conf');
        ProjectFile::setCmds($project_dir,$conf_arr);
        return 'done';
    }

    public function test(Request $request){
        $request->validate(['projectDir'=>'required','name'=>'required']);
        $project_dir=$request->input('projectDir');
        $name=$request->input("name");
        try {
            $cmd = ProjectFile::getTestCmd($project_dir, $name);
        } catch (FileNotFoundException $e) {
            abort(405,"找不到命令配置文件");
        }
        return trim(shell_exec("$cmd 2>&1"));
    }

    public function preprocess(Request $request){}

    public function pick(Request $request){}

}