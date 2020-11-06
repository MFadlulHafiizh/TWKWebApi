<?php

use Illuminate\Http\Request;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post("login", "AuthController@login");
Route::post("register", "AuthController@register");

Route::group(["middleware"=> "auth.jwt"], function() {
    Route::get("logout", "AuthController@logout");
    Route::get('user', 'AuthController@getAuthenticatedUser'); 
});

Route::get('user/data-bug', 'UserDataController@indexBug');
Route::get('user/data-feature', 'UserDataController@indexFeature');
Route::get('user/data-done', 'UserDataController@indexDone');
Route::get('user/getapp', 'UserDataController@userApp');
Route::post('user/report-bug', 'UserDataController@storeBug');