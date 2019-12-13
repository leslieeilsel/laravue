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


    Route::post('index/login', 'Index\IndexController@login');
    Route::post('index/dictData', 'Index\IndexController@dictData');
    Route::post('index/regist', 'Index\IndexController@regist');
    Route::post('index/registUpdate', 'Index\IndexController@registUpdate');
    Route::post('index/application', 'Index\IndexController@application');
    Route::post('index/applicationList', 'Index\IndexController@applicationList');
    Route::post('index/applicationUpdate', 'Index\IndexController@applicationUpdate');

});

Route::post('user/regist', 'User\RegistController@registUser');
Route::get('user/users', 'User\RegistController@getUsers');
Route::post('user/resetPassword', 'User\RegistController@resetPassword');

Route::get('department/getByParentId/{id}', 'System\DepartmentController@getByParentId');
Route::get('department/getAllDepartment', 'System\DepartmentController@getAllDepartment');
Route::post('department/addDepartment', 'System\DepartmentController@add');
Route::post('department/editDepartment', 'System\DepartmentController@edit');

Route::get('dict/dicts', 'System\DictController@dicts');
Route::post('dict/addDict', 'System\DictController@addDict');
Route::post('dict/editDict', 'System\DictController@editDict');
Route::post('dict/deleteDict', 'System\DictController@deleteDict');

Route::post('dict/dictDataList', 'System\DictController@dictDataList');
Route::post('dict/addDictData', 'System\DictController@addDictData');
Route::post('dict/editDictData', 'System\DictController@editDictData');
Route::post('dict/deleteDictData', 'System\DictController@deleteDictData');

Route::get('menu/getmenus', 'System\MenuController@getMenus');
Route::get('menu/getrouter', 'System\MenuController@getRouter');
Route::get('menu/menuselecter', 'System\MenuController@getMenuSelecter');
Route::post('menu/menutree', 'System\MenuController@getMenuTree');
Route::post('menu/add', 'System\MenuController@add');
Route::post('menu/addMenu', 'System\MenuController@addMenu');
Route::post('menu/editMenu', 'System\MenuController@editMenu');
Route::post('menu/deleteMenu', 'System\MenuController@deleteMenu');

Route::get('role/roles', 'System\RoleController@getRoles');
Route::post('role/add', 'System\RoleController@add');
Route::post('role/setDefaultRole', 'System\RoleController@setDefaultRole');
Route::post('role/setrolemenus', 'System\RoleController@setRoleMenus');

Route::post('overviewmonth', 'Api\ApiController@getOverviewMonthData');
Route::get('exportoverviewmonth/{startMonth}/{endMonth}/{type}', 'Api\ApiController@exportOverviewMonthData');

Route::get('log/getOperationLogs', 'Logs\LogController@getOperationLogs');
