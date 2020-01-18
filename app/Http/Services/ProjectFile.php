<?php


namespace App\Http\Services;


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

/**
 * 项目文件的读取与保存逻辑
 * Class self
 * @package App\Http\Services
 */
class ProjectFile
{

    const Disk = 'root';
    const ConfDir = '/.yx/conf';
    const ConfTaskDir = '/.yx/task';
    const CmdsConfFile = 'cmds.json';
    const AppDir = "/app/app";

    static public function clear($project_dir,$modules=Module::Modules){
        $arr=[];
        foreach($modules as $mod){
            if($mod!=Module::Movies and $mod[0]!='.') {
                $arr[] = $project_dir . '/' . $mod;
                $arr[] = $project_dir . self::ConfTaskDir . '/' . $mod;
            }
        }
        foreach($arr as $dir){
            Storage::disk(self::Disk)->deleteDirectory($dir);
        }
    }

    static public function png($project_dir,$module,$name,$ext){
        $path="$project_dir/$module/$name.$ext.png";
        if(!Storage::disk(self::Disk)->exists($path)){
            return '';
        }
        $image_data = file_get_contents($path);
        return 'data:image/png;charset=utf-8;base64,'.base64_encode($image_data);
    }

    static public function existPng($project_dir,$module,$name,$ext){
        $filePath = "$project_dir/$module/$name.$ext";
        $path="$filePath.png";
        return Storage::disk(self::Disk)->exists($path);
    }

    static public function getStar(string $filePath){
        $fp = fopen($filePath,"r");
        $block_step = "";
        $colNames=[];
        $colNum=0;
        $res=[];
        while(!feof($fp)){
            $line=trim(fgets($fp));
            $arr=preg_split("/\s+/",$line);
            if($line=="loop_"){
                $block_step=$line;
            }else if($block_step=="loop_"){
                if(sizeof($arr)==2 && substr($arr[0],0,4)=='_rln'){
                    $colNames[]=$arr[0];
                }else{
                    $block_step="loop_data_";
                    $colNum=sizeof($colNames);
                }
            }else if($block_step=="loop_data_"){
                if(sizeof($arr)==$colNum)$res[]=array_combine($colNames,$arr);
            }
        }
        fclose($fp);
        return $res;
    }

    static public function saveStar(string $filePath, array $json_arr)
    {
        if(sizeof($json_arr)>0){
            $names=array_keys($json_arr[0]);
            $arr=array_map(function($point)use($names){
                $values=array_map(function($name)use($point){
                    if(array_key_exists($name, $point)){
                        return $point[$name];
                    }
                    return "";
                },$names);
                return implode(" ", $values);
            },$json_arr);
            $content = implode("\n", $arr);
            array_walk($names,function(&$name,$k){$name.=" #".($k+1);});
            $header= "loop_\n".implode("\n", $names)."\n";
            Storage::disk('root')->put($filePath,$header.$content);
        }
        return true;
    }


    /**
     *  获取项目下特定模块的图片
     * @param $project_dir string 项目目录
     * @param $module string 子目录
     * @return \Illuminate\Support\Collection ['path','file_name','name','module','ext']
     */
    static public function imgFiles($project_dir,$module){
        $extensions = ['mrc','mrcs','tif','tiff'];
        $raw_list = Storage::disk(self::Disk)->files($project_dir.'/'.$module);
        $files=collect($raw_list)
            ->filter(function($path)use($extensions){
                return in_array(pathinfo($path,PATHINFO_EXTENSION),$extensions);
            })
            ->map(function($path)use($module){
                $path='/'.$path;
                $arr=pathinfo($path);
                //ext
                $ext=$arr['extension'];
                //file_name
                $basename=$arr['basename'];
                //name
                $name=$arr['filename'];
                return compact('path','basename','name','module','ext');
            });
        return $files;
    }

    /**
     * 拷贝默认配置文件到项目目录的/.yx/conf子目录下
     * @param $project_dir string 项目目录
     * @return bool 是否成功
     */
    static public function initConf($project_dir,$raw_root = ''){
        if(!Storage::disk(self::Disk)->exists($project_dir)){
            Storage::disk(self::Disk)->makeDirectory($project_dir);
        }
        // copy conf files which not exist before
        if($raw_root=='' || !Storage::disk(self::Disk)->exists($raw_root)){
            $raw_root=resource_path('conf');
        }else{
            $raw_root=$raw_root.self::ConfDir;
        }
        $new_root = $project_dir.self::ConfDir;
        $files = Storage::disk(self::Disk)->allFiles($raw_root);
        array_walk($files, function($v,$k)use($new_root,$raw_root){
            $new_v = str_replace($raw_root, $new_root, '/'.$v);
            if(!Storage::disk(self::Disk)->exists($new_v)){
                Storage::disk(self::Disk)->copy($v, $new_v);
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
        $path = $project_dir.self::ConfDir.'/'.self::CmdsConfFile;
        $content = Storage::disk(self::Disk)->get($path);
        $json = json_decode($content,true);
        return $json;
    }

    static public function setCmds($project_dir,$arr){
        $path = $project_dir.self::ConfDir.'/'.self::CmdsConfFile;
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
    static private function getParsedCmds($project_dir,$name){
        $path = $project_dir.self::ConfDir.'/'.self::CmdsConfFile;
        $content = Storage::disk(self::Disk)->get($path);
        $content = addslashes($content);
        $app_dir = self::AppDir;
        eval('$content="'.$content.'";');
        $json = json_decode($content,true);
        return $json;
    }

    /**
     * 获取相应模块cmd字符串
     * @param $project_dir
     * @param $module
     * @param $name
     * @return string
     * @throws FileNotFoundException
     */
    static public function getCmd($project_dir,$module,$name){
        $cmds = self::getParsedCmds($project_dir, $name);
        if(array_key_exists($module,$cmds['_current'])){
            $func = $cmds['_current'][$module];
        }else{
            $func = array_keys($cmds[$module])[0];
        }
        $cmd=$cmds[$module][$func];
        $str = trim($cmd['cmd'],';');
        if(array_key_exists('args',$cmd)) {
            foreach ($cmd['args'] as $v) {
                $str .= " ${v['name']} ${v['value']} ";
            }
        }
        if(array_key_exists('cmd_before',$cmd)){
            $str=trim($cmd['cmd_before'],';').';'.$str;
        }
        if(array_key_exists('cmd_after',$cmd)){
            $str=trim($str,';').';'.trim($cmd['cmd_after'],';');
        }
        $cmd_str="cd $project_dir;$str";
        return $cmd_str;
    }

}