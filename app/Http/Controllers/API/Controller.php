<?php

namespace App\Http\Controllers\API;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function response($data=null,$code=2000, $msg=null){
        $content =  array(
            'data'    =>  $data,
            'code'    =>  $code,
            'msg'     =>  $msg
        );
        return response()->json($content);
    }

}
