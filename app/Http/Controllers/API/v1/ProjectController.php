<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
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
        ProjectFile::initConf($project_dir);
        return $project;
    }

    public function png(Request $request){
        $request->validate(['projectDir'=>'required','module'=>'required','name'=>'required','ext'=>'required']);
        $project_dir=$request->input('projectDir');
        $module=$request->input("module");
        $name=$request->input("name");
        $ext=$request->input("ext");
        return ProjectFile::png($project_dir,$module,$name,$ext);
    }

    public function clear(Request $request){
        $request->validate(['projectDir'=>'required']);
        $project_dir=$request->input('projectDir');
        ProjectFile::clear($project_dir);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection ['Movies','MotionCor','CTF','Mark','Pick','Extract']
     */
    public function overview(Request $request){
        $request->validate(['projectDir'=>'required']);
        $project_dir=$request->input('projectDir');
        $files = ProjectFile::imgFiles($project_dir,"Movies");
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

    public function runTest(Request $request){
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
        });
        return $res;
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
        return $res;
    }

    public function getMark(Request $request){
        $request->validate(['projectDir'=>'required','name'=>'required']);
        $project_dir=$request->input('projectDir');
        $name=$request->input("name");
        return Image::getStar($project_dir.'/Mark/'.$name.'_automatch.star');
    }

    public function setMark(Request $request){
        $request->validate(['projectDir'=>'required','name'=>'required','arr'=>'required|array']);
        $project_dir=$request->input('projectDir');
        $name=$request->input("name");
        $arr=$request->input("arr");
        Image::saveStar($project_dir.'/Mark/'.$name.'.star',$arr);
        return 'done';
    }

}