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
Route::group(['as' => 'csv::', 'prefix' => 'csv'], function () {
    Route::get('', 'CsvController@index')->name('index');
    Route::post('', 'CsvController@post')->name('post');
    Route::post('/delete', 'CsvController@delete')->name('delete');
});
