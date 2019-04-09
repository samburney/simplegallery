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

Route::get('/', array('as' => 'home', 'uses' => 'UploadController@getPopular'));

Route::get('get/{path}/{params}', array('as' => 'get', 'uses' => 'UploadController@getGet'));
Route::get('view/{path}/{params?}', array('as' => 'view', 'uses' => 'UploadController@getView'));

Route::any('search/{q?}', array('as' => 'search', 'uses' => 'SearchController@searchIndex'));
Route::get('search/get/{q?}', array('as' => 'search_get', 'uses' => 'SearchController@searchGet'));

Route::controller('upload','UploadController');
Route::controller('uploads','UploadController');

Route::controller('user','UserController');
Route::controller('users','UserController');

Route::controller('collection','CollectionController');
Route::controller('collections','CollectionController');

Route::controller('tag','TagController');
Route::controller('tags','TagController');

Route::controller('password','RemindersController');
Route::controller('passwords','RemindersController');
