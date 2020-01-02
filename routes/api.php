<?php

use Illuminate\Http\Request;

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


Route::group(['prefix'=>'v1','middleware'=>'auth:api'],function(){

	Route::get('/user', 'API\v1\UserController@getUser');
    Route::get('/users', 'API\v1\UserController@getUsers');

    Route::post('/project/create', 'API\v1\ProjectController@create');
    Route::get('/project/overview', 'API\v1\ProjectController@overview');
    Route::get('/mrc', 'API\v1\ProjectController@mrc');

    Route::get('/project/conf', 'API\v1\ProjectController@getConf');
    Route::post('/project/conf', 'API\v1\ProjectController@setConf');

    Route::post('/project/test', 'API\v1\ProjectController@test');

});