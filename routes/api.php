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
    Route::get('ding/getToken', 'Project\DingController@getToken');
    Route::post('ding/userId', 'Project\DingController@userId');
    Route::get('ding/userNotify', 'Project\DingController@userNotify');
    Route::get('ding/getAuditedProjects', 'Project\DingController@getAuditedProjects');
    Route::post('ding/projectProgress', 'Project\DingController@projectProgress');
    Route::post('ding/getAllProjects', 'Project\DingController@getAllProjects');
    Route::post('ding/projectProgressList', 'Project\DingController@projectProgressList');
    Route::post('ding/getAllWarning', 'Project\DingController@getAllWarning');
    Route::post('ding/projectInfo', 'Project\DingController@projectInfo');
    Route::post('ding/projectScheduleInfo', 'Project\DingController@projectScheduleInfo');
    Route::post('ding/actCompleteMoney', 'Project\DingController@actCompleteMoney');
    Route::post('ding/projectPlanInfo', 'Project\DingController@projectPlanInfo');
    Route::post('ding/uploadPic', 'Project\DingController@uploadPic');
    Route::post('ding/auditProjectProgress', 'Project\DingController@auditProjectProgress');
    Route::post('ding/editProjectProgress', 'Project\DingController@editProjectProgress');
    Route::post('ding/allActCompleteMoney', 'Project\DingController@allActCompleteMoney');

    Route::post('user/regist', 'User\RegistController@registUser');
    Route::any('user/users', 'User\RegistController@getUsers');
    Route::post('user/resetPassword', 'User\RegistController@resetPassword');
    Route::post('user/getUserDictData', 'User\RegistController@getUserDictData');
    Route::post('user/deleteUserData', 'User\RegistController@deleteUserData');
    Route::post('user/editRegistUser', 'User\RegistController@editRegistUser');
    Route::post('user/getUser', 'User\RegistController@getUser');

    Route::get('department/getByParentId/{id}', 'System\DepartmentController@getByParentId');
    Route::get('department/getAllDepartment', 'System\DepartmentController@getAllDepartment');
    Route::get('department/getClassDepartment', 'System\DepartmentController@getClassDepartment');
    Route::post('department/addDepartment', 'System\DepartmentController@add');
    Route::post('department/editDepartment', 'System\DepartmentController@edit');

    Route::get('project/getProjects', 'Project\ProjectController@getProjects');
    Route::get('project/getAuditedProjects', 'Project\ProjectController@getAuditedProjects');
    Route::get('project/loadPlan/{id}', 'Project\ProjectController@loadPlan');
    Route::post('project/getAllWarning', 'Project\ProjectController@getAllWarning');
    Route::any('project/getAllProjects', 'Project\ProjectController@getAllProjects');
    Route::post('project/getProjectDictData', 'Project\ProjectController@getProjectDictData');
    Route::post('project/addProject', 'Project\ProjectController@add');
    Route::post('project/edit', 'Project\ProjectController@edit');
    Route::post('project/projectProgress', 'Project\ProjectController@projectProgress');
    Route::any('project/projectProgressList', 'Project\ProjectController@projectProgressList');
    Route::post('project/uploadPic', 'Project\ProjectController@uploadPic');
    Route::post('project/projectPlanInfo', 'Project\ProjectController@projectPlanInfo');
    Route::post('project/getData', 'Project\ProjectController@getData');
    Route::post('project/projectLedgerAdd', 'Project\ProjectController@projectLedgerAdd');
    Route::post('project/projectQuarter', 'Project\ProjectController@projectQuarter');
    Route::post('project/editProjectProgress', 'Project\ProjectController@editProjectProgress');
    Route::post('project/auditProjectProgress', 'Project\ProjectController@auditProjectProgress');
    Route::post('project/buildPlanFields', 'Project\ProjectController@buildPlanFields');
    Route::post('project/auditProject', 'Project\ProjectController@auditProject');
    Route::post('project/getEditFormData', 'Project\ProjectController@getEditFormData');
    Route::post('project/toAudit', 'Project\ProjectController@toAudit');
    Route::post('project/toAuditSchedule', 'Project\ProjectController@toAuditSchedule');
    Route::post('project/actCompleteMoney', 'Project\ProjectController@actCompleteMoney');
    Route::post('project/getProjectNoScheduleList', 'Project\ProjectController@getProjectNoScheduleList');
    Route::post('project/projectScheduleMonth', 'Project\ProjectController@projectScheduleMonth');
    Route::post('project/noAudit', 'Project\ProjectController@noAudit');
    Route::post('project/projectDelete', 'Project\ProjectController@projectDelete');
    Route::post('project/projectScheduleDelete', 'Project\ProjectController@projectScheduleDelete');
    Route::post('project/locationPosition', 'Project\ProjectController@locationPosition');
    Route::post('project/getProjectById', 'Project\ProjectController@getProjectById');

// 添加台账导出
    Route::get('project/exportSchedule', 'Project\LedgerController@exportSchedule');
    Route::get('project/exportLedger', 'Project\LedgerController@export');
    Route::post('project/projectLedgerList', 'Project\LedgerController@projectLedgerList');
    Route::get('project/downLoadSchedule', 'Project\LedgerController@downLoadSchedule');
    Route::get('project/exportProject', 'Project\LedgerController@exportProject');

//项目调整
    Route::post('project/projectAdjustment', 'Project\ProjectController@projectAdjustment');

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

    Route::post('role/deleteRoleData', 'System\RoleController@deleteRoleData');
    Route::get('role/roles', 'System\RoleController@getRoles');
    Route::post('role/add', 'System\RoleController@add');
    Route::post('role/edit', 'System\RoleController@edit');
    Route::post('role/setDefaultRole', 'System\RoleController@setDefaultRole');
    Route::post('role/setrolemenus', 'System\RoleController@setRoleMenus');
    Route::post('role/getDepartmentTree', 'System\RoleController@getDepartmentTree');
    Route::post('role/editRoleDep', 'System\RoleController@editRoleDep');

    Route::get('log/getOperationLogs', 'Logs\LogController@getOperationLogs');
    Route::get('datav/project', 'DataV\ProjectController@getDataVProject');
});

