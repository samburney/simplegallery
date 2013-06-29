<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array('as' => 'home', 'uses' => 'UploadController@getIndex'));

Route::get('get/{path}/{params}', array('as' => 'get', 'uses' => 'UploadController@getGet'));
Route::get('view/{path}/{params}', array('as' => 'view', 'uses' => 'UploadController@getView'));

Route::controller('upload','UploadController');