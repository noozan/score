<?php

use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([
    'middleware'=>'api',
    'prefix'=>'user',
    //'as'=>'api.',
], function($router) {
    Route::post('register', 'UserController@register');
    Route::post('login', 'UserController@login');
    Route::post('logout', 'UserController@logout');
    Route::post('profile', 'UserController@profile');
    Route::post('refresh', 'UserController@refresh');
    Route::post('recharge', 'UserController@reload');
});
Route::group([
    'middleware'=>'api',
   'prefix'=>'games'

], function($router) {
    Route::post('/all', 'CompetitionController@index');
    Route::post('/join', 'CompetitionController@join');

});
