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

use App\Http\Services\Image;

Route::get('/', 'Web\AppController@getApp')
->middleware('auth');

Auth::routes(['register' => false]);

Route::get('/test.png', function(){
    //Image::motionTracePng("/media/zhangtaotao/软件/work/xinyue/test/MotionCor","May08_03.05.02.bin");
});

