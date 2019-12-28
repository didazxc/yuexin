<?php


namespace App\Http\Services;


use Illuminate\Support\Facades\Storage;

class ProjectFile
{
    /**
     * @param $project_dir
     * @return \Illuminate\Support\Collection ['path','file_name','name','module','ext']
     */
    static public function movies($project_dir){
        $module = 'Movies';
        $extensions = ['mrc','mrcs','tif','tiff'];
        $raw_list = Storage::disk('root')->files($project_dir.'/'.$module);
        $files=collect($raw_list)
            ->filter(function($path)use($extensions){
                return in_array(pathinfo($path,PATHINFO_EXTENSION),$extensions);
            })
            ->map(function($path)use($module){
                $path='/'.$path;
                //ext
                $ext=pathinfo($path,PATHINFO_EXTENSION);
                //file_name
                $file_name=pathinfo($path,PATHINFO_BASENAME);
                //name
                $name=preg_replace('/\.'.$ext.'$/','',$file_name);
                return compact('path','file_name','name','module','ext');
            });
        return $files;
    }
}