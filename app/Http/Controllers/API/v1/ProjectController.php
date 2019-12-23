<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Services\Image;
use Illuminate\Support\Facades\Storage;

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

    public function files(Request $request){
        $dir=$request->input('dir');
        $ext=$request->input('ext','mrc');
        return  Image::files($dir,[$ext]);
    }

    public function mrc(Request $request){
        $path=$request->input('path');
        return Image::mrc2png($path);
    }

}