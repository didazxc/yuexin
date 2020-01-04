<?php


namespace App\Http\Services;


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class ProjectFile
{

    private static $disk = 'root';
    private static $confDir = '/.yx/conf';
    private static $cmdsConfFile = 'cmds.json';
    private static $appDir = "/media/zhangtaotao/软件/work/xinyue/app";

    /**
     *  获取项目下特定模块的图片
     * @param $project_dir string 项目目录
     * @param $module string 子目录
     * @return \Illuminate\Support\Collection ['path','file_name','name','module','ext']
     */
    static public function imgFiles($project_dir,$module){
        $extensions = ['mrc','mrcs','tif','tiff'];
        $raw_list = Storage::disk(ProjectFile::$disk)->files($project_dir.'/'.$module);
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

    /**
     * 拷贝默认配置文件到项目目录的/.yx/conf子目录下
     * @param $project_dir string 项目目录
     * @return bool 是否成功
     */
    static public function initConf($project_dir){
        if(!Storage::disk(ProjectFile::$disk)->exists($project_dir)){
            Storage::disk(ProjectFile::$disk)->makeDirectory($project_dir);
        }
        // copy conf files which not exist before
        $new_root = $project_dir.ProjectFile::$confDir;
        $raw_root = resource_path('conf');
        $files = Storage::disk(ProjectFile::$disk)->allFiles($raw_root);
        array_walk($files, function($v,$k)use($new_root,$raw_root){
            $new_v = str_replace($raw_root, $new_root, '/'.$v);
            if(!Storage::disk(ProjectFile::$disk)->exists($new_v)){
                Storage::disk(ProjectFile::$disk)->copy($v, $new_v);
            }
        });
        return true;
    }

    /**
     * 获取cmds配置
     * @param $project_dir
     * @return mixed
     * @throws FileNotFoundException
     */
    static public function getCmds($project_dir){
        $path = $project_dir.ProjectFile::$confDir.'/'.ProjectFile::$cmdsConfFile;
        $content = Storage::disk(ProjectFile::$disk)->get($path);
        $json = json_decode($content,true);
        return $json;
    }

    static public function setCmds($project_dir,$arr){
        $path = $project_dir.ProjectFile::$confDir.'/'.ProjectFile::$cmdsConfFile;
        Storage::disk('root')->put($path,json_encode($arr));
        return true;
    }

    /**
     * 获取参数替换后的cmds
     * @param $project_dir string 项目目录
     * @param $name string 执行文件的名称，去掉后缀
     * @return mixed json解析后的数组
     * @throws FileNotFoundException
     */
    static public function getParsedCmds($project_dir,$name){
        $path = $project_dir.ProjectFile::$confDir.'/'.ProjectFile::$cmdsConfFile;
        $content = Storage::disk(ProjectFile::$disk)->get($path);
        $content = addslashes($content);
        $app_dir = ProjectFile::$appDir;
        eval('$content="'.$content.'";');
        $json = json_decode($content,true);
        return $json;
    }

    /**
     * 获取测试使用的cmd字符串
     * @param $project_dir
     * @param $name
     * @return string
     * @throws FileNotFoundException
     */
    static public function getTestCmd($project_dir,$name){
        $cmds = ProjectFile::getParsedCmds($project_dir, $name);
        $cmd_arr = array_map(function($cmd){
            $str = trim($cmd['cmd_before'],';').";".trim($cmd['cmd'],';');
            foreach($cmd['args'] as $v){
                $str.=" ${v['name']} ${v['value']} ";
            }
            return $str;
        },$cmds);
        $cmd_str=trim(implode(";",$cmd_arr),';');
        $cmd="cd $project_dir;$cmd_str";
        return $cmd;
    }

}