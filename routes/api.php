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
Route::get("notification", "UserDataController@getListNotif");
Route::patch("notification/readat/{id_notif}", 'UserDataController@updateNotifReadAt');
Route::post('testBase64Image/{id}', 'UserDataController@testBase64Image');

Route::group(["middleware"=> "jwt.auth"], function(){
    Route::get("logout/{id}", "AuthController@logout");
    Route::get('user', 'AuthController@getAuthenticatedUser');
});

Route::get('pushnotif', 'UserDataController@pushNotifBug');
Route::get('getfcm/', 'UserDataController@getFcmToken');

Route::post('user/upload-image-base64/{id}', 'UserDataController@uploadImageDecoded');
Route::post('user/upload-image/{id}', 'UserDataController@uploadImageFile');

Route::get('user/assign-at', 'UserDataController@assignAt');

Route::patch('user/agreement-act/{id_ticket}', ['middleware' => 'api.role:client-head', 'uses' => 'UserDataController@agreementAct']);

Route::group(['middleware'=> 'api.role:client-head,client-staff'], function() {
    Route::get('user/data-bug', 'UserDataController@filterData');
    Route::get('user/data-feature', 'UserDataController@indexFeature');
    Route::get('user/data-done', 'UserDataController@indexDone');
    Route::get('user/getapp', 'UserDataController@userApp');
    Route::post('user/report-bug', 'UserDataController@storeBug');
    Route::post('user/request-feature', 'UserDataController@storeFeature');
});

Route::get('testurl', 'AdminController@assignSendNotif');

Route::group(["middleware"=> "api.role:twk-head"], function(){
    Route::get('admin/data-bug', 'AdminController@indexBugAdmin');
    Route::get('admin/data-feature', 'AdminController@indexFeatureAdmin');
    Route::get('admin/data-done', 'AdminController@indexDoneAdmin');
    Route::get('admin/getStaff', 'AdminController@getTwkStaff');
    Route::patch('admin/make-agreement/{id_ticket}', 'AdminController@makeAgreement');
    Route::post('admin/statusChange', 'AdminController@changeStatus');
    Route::post('admin/assignment', 'AdminController@assignTask');
});

Route::group(['middleware' => 'api.role:twk-staff'], function () {
    Route::get('twkstaff/todo', 'TwkStaffController@indexToDo');
    Route::get('twkstaff/hasdone', 'TwkStaffController@indexHasDone');
    Route::get('twkstaff/listnotif', 'TwkStaffController@listNotif');
    Route::patch('twkstaff/markAsComplete/{id_ticket}', 'TwkStaffController@markAsComplete');
});