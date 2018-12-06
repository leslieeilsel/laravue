<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'api'], function ($router) {
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('user', 'AuthController@user');
});

Route::post('tabledatas', 'Report\FSaleMonthController@getSaleDayData');
Route::post('user/regist', 'User\RegistController@registUser');
Route::get('user/users', 'User\RegistController@getUsers');
Route::get('department/depts', 'Department\DepartmentController@getDepts');
Route::get('department/deptselecter', 'Department\DepartmentController@getDeptSelecter');
Route::post('department/add', 'Department\DepartmentController@add');

Route::post('overviewmonth', 'Api\ApiController@getOverviewMonthData');
Route::get('exportoverviewmonth/{startMonth}/{endMonth}/{type}', 'Api\ApiController@exportOverviewMonthData');
