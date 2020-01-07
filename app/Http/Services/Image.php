<?php
namespace App\Http\Services;

use Illuminate\Support\Facades\Storage;

class Image
{
    static public function mrc2Png($filePath,$outFilePath){
        $fh = fopen($filePath, "rb");
        $head=unpack('LNX/LNY/LNZ/LMODE',fread($fh, 1024));
        $nx = $head['NX'];
        $ny = $head['NY'];
        $nz = $head['NZ'];
        $length = $nx*$ny*$nz;
        $content=fread($fh,$length*32);
        $rawArr=unpack('f'.$length,$content);
        fclose($fh);
        //平均
        $a=[0];
        $len = $nx*$ny;
        for($i=1;$i<$len+1;$i++){
            $sum=$rawArr[$i];
            for($n=1;$n<$nz;$n++){
                $sum+=$rawArr[$i+$len];
            }
            $a[]=$sum/$nz;
        }
        //生成GD图片
        $img = imageCreate($nx, $ny);
        $c_arr=[];
        foreach(range(0,255) as $i){
            $c_arr[]=imageColorAllocate($img,$i,$i,$i);
        }
        //blur
        if(false){
            $arr=[0];
            foreach($a as $k=>$v){
                $row=intval(($k-1)/$nx);
                $col=($k-1)-$row*$nx;
                $v=0.0947416*($row==0||$col==0?$v:$a[($row-1)*$nx+$col])+
                    0.118318*($row==0?$v:$a[($row-1)*$nx+$col+1])+
                    0.0947416*($row==0||$col>=($nx-1)?$v:$a[($row-1)*$nx+$col+2])+

                    0.118318*($col==0?$v:$a[$row*$nx+$col])+
                    0.147761*$v+
                    0.118318*($col>=($nx-1)?$v:$a[$row*$nx+$col+2])+

                    0.0947416*($row>=($ny-1)||$col==0?$v:$a[($row+1)*$nx+$col])+
                    0.118318*($row>=($ny-1)?$v:$a[($row+1)*$nx+$col+1])+
                    0.0947416*($row>=($ny-1)||$col>=($nx-1)?$v:$a[($row+1)*$nx+$col+2]);
                $arr[]=$v;
            };
        }else{
            $arr=$a;
        }
        //png
        $min=10000;
        $max=0;
        $arr=array_map(function($v)use(&$min,&$max){
            $res=is_nan($v)?0:$v;
            if($res<$min){$min=$res;}else if($res>$max){$max=$res;}
            return $res;
        },$arr);
        $range = $max-$min;
        foreach($arr as $k=>$v){
            $row=intval(($k-1)/$nx);
            $col=($k-1)-$row*$nx;
            $value = intval(($v-$min)*255/$range);
            imagesetpixel($img, $col, $row, $c_arr[$value]);
        };
        //返回图片
        header("Content-Type: image/png");
        imagepng($img,$outFilePath);
        imagedestroy($img);

        /*ob_start ();
        imagepng ($img);
        $image_data = ob_get_contents ();
        ob_end_clean ();
        imagedestroy($img);
        return 'data:image/png;charset=utf-8;base64,'.base64_encode($image_data);*/

    }

    static private function files($dir,$extensions){
        $raw_list = Storage::disk('root')->allFiles($dir);
        $files=collect($raw_list)
            ->filter(function($path)use($extensions){
                $extension=pathinfo($path,PATHINFO_EXTENSION);
                return in_array($extension,$extensions);
            })
            ->map(function($path)use($dir){
                $path='/'.$path;
                //ext
                $ext=pathinfo($path,PATHINFO_EXTENSION);
                //file_name
                $file_name=pathinfo($path,PATHINFO_BASENAME);
                $name=preg_replace('/\.'.$ext.'$/','',$file_name);
                $name=explode('_',$name,1)[0];
                //module
                $path_dir=pathinfo($path,PATHINFO_DIRNAME);
                $module=substr($path_dir,strlen($dir));
                $module=strlen($module)>0?substr($module,1):"";
                //src
                $src='';
                //mark
                $mark='good';
                return compact('path','file_name','name','module','ext','src','mark');
            })
            ->filter(function($item){
                return !in_array('.task',explode('/',$item['module']));
            })
            ->groupBy('name')
            ->map(function($item){return $item->groupBy('module');})
            ->filter(function($item){
                return $item->has('Movies');
            })
            ->values();
        return $files;
    }

    static private function getAlnData($filePath){
        $res=[];
        $module='';
        $index=0;
        $fp = fopen($filePath,"r");
        while(!feof($fp)){
            $line=trim(fgets($fp),"\n");
            $first=substr($line,0,1);
            if($first!=' '){
                $module=$line;
                $res[$module]=[];
            }else{
                $line=trim($line);
                $arr=preg_split("/\s+/",$line);
                if($module=='setting'){
                    $res[$module][trim($arr[0],':')]=$arr;
                }else if($module=='globalShift'){
                    if($arr[0]!='stackID')$res[$module][$arr[0]]=$arr;
                }else{
                    if($arr[0]=='patchID:'){
                        $index=$arr[1];
                    }else if($arr[0]!='Converge:'){
                        $res[$module][$index][$arr[0]]=$arr;
                    }
                }

            }
        }
        fclose($fp);
        return $res;
    }

    static public function motion2Png($filePath,$outFilePath){
        $data=Image::getAlnData($filePath);
        //图片大小
        $nX=$data['setting']['stackSize'][1];
        $nY=$data['setting']['stackSize'][2];
        //行列数
        $gridX=$data['setting']['patches'][1];
        $gridY=$data['setting']['patches'][2];
        //行高与列宽
        $gridXL=$nX/$gridX;
        $gridYL=$nY/$gridY;
        //生成GD图片
        if($gridX>0 && $gridY>0){
            $img = imageCreate($nX,$nY);
            $c_arr=['white'=>imageColorAllocate($img,255,255,255),
                'blue'=>imageColorAllocate($img,0,0,255),
                'red'=>imageColorAllocate($img,255,0,0),
                'gray'=>imageColorAllocate($img,211,211,211)];
            for($i=1;$i<$gridX;$i++){
                $row = $i*$gridYL;
                imageline($img,0,$row,$nX,$row,$c_arr['gray']);
            }
            for($j=1;$j<$gridY;$j++){
                $col = $j*$gridXL;
                imageline($img,$col,0,$col,$nY,$c_arr['gray']);
            }
            //中心
            $cX=$nX/2;
            $cY=$nY/2;
            imagesetpixel($img, $cX, $cY, $c_arr['red']);
            $lastX=$cX;
            $lastY=$cY;
            foreach($data['globalShift'] as $v){
                imageline($img,$lastX,$lastY,$cX+$v[1],$cY+$v[2],$c_arr['red']);
                $lastX=$cX+$v[1];
                $lastY=$cY+$v[2];
            }
            //patches
            foreach($data['localShift'] as $patch){
                $lastX=$patch[0][1];
                $lastY=$patch[0][2];
                foreach($patch as $v) {
                    if(count($v)>4){
                        imageline($img, $lastX, $lastY, $v[1] + $v[3], $v[2] + $v[4], $c_arr['blue']);
                        $lastX=$v[1]+$v[3];
                        $lastY=$v[2]+$v[4];
                    }
                }
            }

            //展示
            header("Content-Type: image/png");
            imagepng($img,$outFilePath);
            imagedestroy($img);

            /*ob_start ();
            imagepng ($img);
            $image_data = ob_get_contents ();
            ob_end_clean ();
            imagedestroy($img);
            return 'data:image/png;charset=utf-8;base64,'.base64_encode($image_data);*/
        }
        //return "";
    }

    static public function lowPassPngStr($filePath){
        return '';
    }

    static public function contrastPngStr($filePath){
        return '';
    }

    /**
     * 获取star文件中的数据
     * @param string $filePath
     * @return array
     */
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

    /**
     * 将数组保存为star格式
     * @param string $filePath
     * @param array $json_arr
     * @return bool
     */
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

}