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

Route::post('user/regist', 'User\RegistController@registUser');
Route::get('user/users', 'User\RegistController@getUsers');
Route::post('user/resetPassword', 'User\RegistController@resetPassword');

Route::get('department/getByParentId/{id}', 'System\DepartmentController@getByParentId');
Route::get('department/getAllDepartment', 'System\DepartmentController@getAllDepartment');
Route::post('department/addDepartment', 'System\DepartmentController@add');
Route::post('department/editDepartment', 'System\DepartmentController@edit');

Route::get('project/getProjects', 'Project\ProjectController@getProjects');
Route::get('project/loadPlan/{id}', 'Project\ProjectController@loadPlan');
Route::get('project/getAllWarning', 'Project\ProjectController@getAllWarning');
Route::get('project/getAllProjects', 'Project\ProjectController@getAllProjects');
Route::post('project/getProjectDictData', 'Project\ProjectController@getProjectDictData');
Route::post('project/addProject', 'Project\ProjectController@add');
Route::post('project/addProjectPlan', 'Project\ProjectController@addProjectPlan');
Route::post('project/edit', 'Project\ProjectController@edit');
Route::post('project/deleteProject', 'Project\ProjectController@delete');
Route::post('project/projectProgress', 'Project\ProjectController@projectProgress');
Route::post('project/projectProgressList', 'Project\ProjectController@projectProgressList');
Route::post('project/uploadPic', 'Project\ProjectController@uploadPic');
Route::post('project/projectPlanInfo', 'Project\ProjectController@projectPlanInfo');
Route::post('project/getData', 'Project\ProjectController@getData');
Route::post('project/projectLedgerAdd', 'Project\ProjectController@projectLedgerAdd');
Route::post('project/projectLedgerList', 'Project\ProjectController@projectLedgerList');
Route::post('project/projectQuarter', 'Project\ProjectController@projectQuarter');
Route::post('project/editProjectProgress', 'Project\ProjectController@editProjectProgress');
Route::post('project/auditProjectProgress', 'Project\ProjectController@auditProjectProgress');
Route::post('project/buildPlanFields', 'Project\ProjectController@buildPlanFields');
// 添加台账导出
Route::get('project/exportLedger', 'Project\LedgerController@export');

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

Route::get('log/getOperationLogs', 'Logs\LogController@getOperationLogs');