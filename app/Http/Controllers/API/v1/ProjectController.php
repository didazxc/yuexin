<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Services\ProjectFile;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Services\Image;

class ProjectController extends Controller
{
    public function create(Request $request){
        $request->validate(['project'=>'required']);
        $form=$request->input('project');
        $project=Project::create([
            'user_id'=>$request->user()->id,
            'name'=>$form['name'],
            'directory'=>$form['directory'],
            'ssd_directory'=>$form['directory'],
            'args'=>$form['map'],
        ]);
        return $project;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection ['Movies','MotionCor','CTF','Mark','Pick','Extract']
     */
    public function overview(Request $request){
        $project_dir=$request->input('project');
        $files = ProjectFile::movies($project_dir);
        //整合MotionCor和CTF模块
        $res=$files->map(function($it)use($project_dir){
            $name=$it['name'];
            $item=[];
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
        $path=$request->input('path');
        return Image::mrc2png($path);
    }

    public function preprocess(Request $request){}

    public function pick(Request $request){}

}