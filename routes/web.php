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

Route::get('/', 'Web\AppController@getApp')
->middleware('auth');

Auth::routes(['register' => false]);

Route::get('/test', function(){
    $user=App\User::find(0);
    if(!$user){
        App\User::create(['name'=>'admin','email'=>'admin1@admin.com','password'=>'administrator']);
        echo 2;
    }
});

