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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::post('/', 'HomeController@postIndex')->name('post_home');
Route::delete('/', 'HomeController@deleteIndex')->name('delete_home');

Route::get('/create', 'HomeController@create')->name('create');
Route::post('/create', 'HomeController@postCreate')->name('post_create');