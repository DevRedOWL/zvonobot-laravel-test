<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::post('/getDialogs', [
    'middleware' => 'auth',
    'uses' => 'MessagesController@GetDialogs'
]);
Route::post('/getMessages', [
    'middleware' => 'auth',
    'uses' => 'MessagesController@GetMessages'
]);
Route::post('/sendMessage', [
    'middleware' => 'auth',
    'uses' => 'MessagesController@SendMessage'
]);

Route::get('/messages', 'HomeController@index');
Route::get('/design', 'HomeController@design');
