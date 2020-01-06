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
    Route::get('ding/getToken', 'Integral\DingController@getToken');
    Route::post('ding/userId', 'Integral\DingController@userId');
    Route::post('ding/salesDataList', 'Integral\DingController@salesDataList');
    Route::post('ding/salesDataAdd', 'Integral\DingController@salesDataAdd');
    Route::post('ding/salesData', 'Integral\DingController@salesData');
    Route::post('ding/dictData', 'Integral\DingController@dictData');
    Route::post('ding/userInfo', 'Integral\DingController@userInfo');
    Route::post('ding/importIntegral', 'Integral\DingController@importIntegral');
    Route::get('ding/getActivityToken', 'Integral\DingController@getActivityToken');
    Route::post('ding/activityUserId', 'Integral\DingController@activityUserId');
    Route::post('ding/activityPlan', 'Integral\DingController@activityPlan');
    Route::post('ding/activityImplement', 'Integral\DingController@activityImplement');
    Route::post('ding/activityPlanInfo', 'Integral\DingController@activityPlanInfo');
    Route::post('ding/activityImplementAdd', 'Integral\DingController@activityImplementAdd');
    Route::post('ding/uploadPic', 'Integral\DingController@uploadPic');
});

Route::post('user/regist', 'User\RegistController@registUser');
Route::get('user/users', 'User\RegistController@getUsers');
Route::post('user/resetPassword', 'User\RegistController@resetPassword');

Route::get('department/getByParentId/{id}', 'System\DepartmentController@getByParentId');
Route::get('department/getAllDepartment', 'System\DepartmentController@getAllDepartment');
Route::post('department/addDepartment', 'System\DepartmentController@add');
Route::post('department/editDepartment', 'System\DepartmentController@edit');

Route::post('value/valueIntegralLogs', 'Integral\IntegralController@valueIntegralLogs');
Route::post('value/valueIntegralLogsDel', 'Integral\IntegralController@valueIntegralLogsDel');
Route::post('value/devIntegralLogs', 'Integral\IntegralController@devIntegralLogs');
Route::post('value/devIntegralLogsDel', 'Integral\IntegralController@devIntegralLogsDel');
Route::post('value/valueIntegralList', 'Integral\IntegralController@valueIntegralList');
Route::post('value/devIntegralList', 'Integral\IntegralController@devIntegralList');
Route::post('value/salesDataList', 'Integral\IntegralController@salesDataList');
Route::post('value/areaMeritsAimList', 'Integral\IntegralController@areaMeritsAimList');
Route::post('value/areaMeritsAimAdd', 'Integral\IntegralController@areaMeritsAimAdd');
Route::post('value/areaMeritsAimEdit', 'Integral\IntegralController@areaMeritsAimEdit');
Route::post('value/areaMeritsAimDel', 'Integral\IntegralController@areaMeritsAimDel');
Route::post('value/salesDataAdd', 'Integral\IntegralController@salesDataAdd');
Route::post('value/salesDataEdit', 'Integral\IntegralController@salesDataEdit');
Route::post('value/salesDataDel', 'Integral\IntegralController@salesDataDel');
Route::post('value/videoPatrolList', 'Integral\IntegralController@videoPatrolList');
Route::post('value/videoPatrolEdit', 'Integral\IntegralController@videoPatrolEdit');
Route::post('value/videoPatrolDel', 'Integral\IntegralController@videoPatrolDel');
Route::post('value/superviseServiceList', 'Integral\IntegralController@superviseServiceList');
Route::post('value/superviseServiceAdd', 'Integral\IntegralController@superviseServiceAdd');
Route::post('value/superviseServiceEdit', 'Integral\IntegralController@superviseServiceEdit');
Route::post('value/superviseServiceDel', 'Integral\IntegralController@superviseServiceDel');
Route::post('value/salesData', 'Integral\IntegralController@salesData');
Route::post('value/dictData', 'Integral\IntegralController@dictData');
Route::post('value/dictDataSupervise', 'Integral\IntegralController@dictDataSupervise');
Route::post('value/importIntegral', 'Integral\IntegralController@importIntegral');
Route::post('value/importValueIntegral', 'Integral\IntegralController@importValueIntegral');
Route::post('value/activityPlan', 'Integral\IntegralController@activityPlan');
Route::post('value/activityPlanAdd', 'Integral\IntegralController@activityPlanAdd');
Route::post('value/activityPlanEdit', 'Integral\IntegralController@activityPlanEdit');
Route::post('value/activityPlanDel', 'Integral\IntegralController@activityPlanDel');
Route::post('value/activityImplement', 'Integral\IntegralController@activityImplement');
Route::post('value/activityImplementAdd', 'Integral\IntegralController@activityImplementAdd');
Route::post('value/activityImplementEdit', 'Integral\IntegralController@activityImplementEdit');
Route::post('value/activityImplementDel', 'Integral\IntegralController@activityImplementDel');
Route::post('value/departmentList', 'Integral\IntegralController@departmentList');
Route::post('value/departmentInfo', 'Integral\IntegralController@departmentInfo');
Route::post('value/videoPatrolAdd', 'Integral\IntegralController@videoPatrolAdd');
Route::post('value/areaDepartmentList', 'Integral\IntegralController@areaDepartmentList');
Route::post('value/areaShop', 'Integral\IntegralController@areaShop');
Route::post('value/uploadPic', 'Integral\IntegralController@uploadPic');
Route::post('value/uploadVideo', 'Integral\IntegralController@uploadVideo');




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
