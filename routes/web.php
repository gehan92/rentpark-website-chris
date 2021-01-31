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

Route::get('/', 'HomeController@index');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/user_chat', 'SampleController@user_chat')->name('user_chat');

Route::get('/provider_chat', 'SampleController@provider_chat')->name('provider_chat');


Route::get('pages' , 'ApplicationController@static_pages')->name('static_pages.view');