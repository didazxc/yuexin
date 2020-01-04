<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Services\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function getMrc(Request $request){
        $request->validate(['path'=>'required']);
        $path=$request->input('path');
        return Image::mrc2png($path);
    }
}