<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::post('user/create', 'UserController@create');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Route::get('/competitions', function () {
    $posts = App\User::find(1);
    //$posts=$posts
    return $posts->transactions;//view('home', compact('posts'));
});
