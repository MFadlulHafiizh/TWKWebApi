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

Route::group(["middleware"=> "jwt.auth"], function(){
    Route::get("logout", "AuthController@logout");
    Route::get('user', 'AuthController@getAuthenticatedUser');
});

Route::get('pushnotif', 'UserDataController@pushNotifBug');

Route::post('user/upload-image/{id}', 'UserDataController@uploadImage');

Route::group(["middleware"=> "api.role:client-head"], function() {
    Route::get('user/data-bug', 'UserDataController@indexBug');
    Route::get('user/data-feature', 'UserDataController@indexFeature');
    Route::get('user/data-done', 'UserDataController@indexDone');
    Route::get('user/getapp', 'UserDataController@userApp');
    Route::post('user/report-bug', 'UserDataController@storeBug');
    Route::post('user/request-feature', 'UserDataController@storeFeature');
});

Route::group(["middleware"=> "api.role:twk-head"], function(){
    Route::get('admin/data-bug', 'AdminController@indexBugAdmin');
    Route::get('admin/data-feature', 'AdminController@indexFeatureAdmin');
    Route::get('admin/data-done', 'AdminController@indexDoneAdmin');
    Route::patch('admin/make-agreement/{id_ticket}', 'AdminController@makeAgreement');
});
