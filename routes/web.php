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
header("Access-Control-Allow-Origin: *");
Route::get('/', function () {
    return view('welcome');
});
Route::post('/test/register','TestController@register');
Route::any('/test/login','TestController@login');
Route::any('/test/showTime','TestController@showTime');
Route::any('/goods/create','TestController@create');
Route::post('/test/check2','TestController@check2'); 	// 验证签名
