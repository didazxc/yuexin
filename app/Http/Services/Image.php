<?php
namespace App\Http\Services;

use Illuminate\Support\Facades\Storage;

class Image
{
    static public function mrc2png($filePath){
        $fh = fopen($filePath, "rb");
        $head=unpack('LNX/LNY/LNZ/LMODE',fread($fh, 1024));
        $nx = $head['NX'];
        $ny = $head['NY'];
        $length = $nx*$ny*1;
        $content=fread($fh,$length*32);
        $a=unpack('f'.$length,$content);
        fclose($fh);
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
        $min = min($arr);
        $range = max($arr)-$min;
        foreach($arr as $k=>$v){
            $row=intval(($k-1)/$nx);
            $col=($k-1)-$row*$nx;
            $value = intval(($v-$min)*255/$range);
            imagesetpixel($img, $col, $row, $c_arr[$value]);
        };
        //返回图片
        /*header("Content-Type: image/png");
        imagepng($img);
        imagedestroy($img);*/
        ob_start ();
        imagepng ($img);
        $image_data = ob_get_contents ();
        ob_end_clean ();
        imagedestroy($img);
        return 'data:image/png;charset=utf-8;base64,'.base64_encode($image_data);
    }

    static public function files($dir,$extensions){
        $raw_list = Storage::disk('root')->allFiles($dir);
        $files=collect($raw_list)
            ->filter(function($path)use($extensions){
                $extension=pathinfo($path,PATHINFO_EXTENSION);
                return in_array($extension,$extensions);
            })->map(function($path)use($dir){
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
                //src
                $src='';
                //mark
                $mark='good';
                return compact('path','file_name','name','module','ext','src','mark');
            })->filter(function($item){
                return !in_array('.task',explode('/',$item['module']));
            })->groupBy('name')->map(function($item){return $item->groupBy('module');})->values();
        return $files;
    }

}