<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix'=>'v1','middleware'=>'auth:api','namespace'=>'\App\Http\Controllers\API\v1'],function(){

	Route::get('/user', 'UserController@getUser');
    Route::get('/users', 'UserController@getUsers');

    Route::get('/mrc', 'ImageController@getMrc');

    Route::group(['prefix'=>'project'],function(){
        Route::post('/create', 'ProjectController@create');

        Route::get('/overview', 'ProjectController@overview');
        Route::get('/conf', 'ProjectController@getConf');
        Route::post('/conf', 'ProjectController@setConf');
        Route::post('/test', 'ProjectController@runTest');

        Route::get('/preprocess', 'ProjectController@preprocess');
        Route::get('/pick', 'ProjectController@pick');
        Route::get('/pick/mark', 'ProjectController@getMark');
        Route::post('/pick/mark', 'ProjectController@setMark');
    });

});