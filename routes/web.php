<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Services\ProjectFile;

Route::get('/', 'Web\AppController@getApp')
->middleware('auth');

Auth::routes(['register' => false]);

Route::get('/test', function(){
    $project_dir="/media/zhangtaotao/软件/work/xinyue/test";
    $module="MotionCor";
    $file="/media/zhangtaotao/软件/work/xinyue/test/Movies/May08_03.05.02.bin.mrc";
    dd(pathinfo($file));
    //Image::motionTracePng("/media/zhangtaotao/软件/work/xinyue/test/MotionCor","May08_03.05.02.bin");
});

