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
    $user = $request->user();
    $user->roleName = \App\Models\Role::find($user->group_id)->name;
    return $user;
});

Route::post('password/reset', 'Auth\ResetPasswordController@reset');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');

Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');
Route::post('refresh', 'AuthController@refresh');
Route::post('user', 'AuthController@user');


Route::group(['middleware' => ['api']], function () {
    // 用户管理
    Route::post('user/regist', 'User\RegistController@registUser');
    Route::any('user/users', 'User\RegistController@getUsers');
    Route::post('user/resetPassword', 'User\RegistController@resetPassword');
    Route::post('user/getUserDictData', 'User\RegistController@getUserDictData');
    Route::post('user/deleteUserData', 'User\RegistController@deleteUserData');
    Route::post('user/editRegistUser', 'User\RegistController@editRegistUser');
    Route::post('user/getUser', 'User\RegistController@getUser');

    // 部门管理
    Route::get('department/getByParentId/{id}', 'System\DepartmentController@getByParentId');
    Route::get('department/getAllDepartment', 'System\DepartmentController@getAllDepartment');
    Route::get('department/getClassDepartment', 'System\DepartmentController@getClassDepartment');
    Route::post('department/addDepartment', 'System\DepartmentController@add');
    Route::post('department/editDepartment', 'System\DepartmentController@edit');

    // 数据字典管理
    Route::get('dict/dicts', 'System\DictController@dicts');
    Route::post('dict/addDict', 'System\DictController@addDict');
    Route::post('dict/editDict', 'System\DictController@editDict');
    Route::post('dict/deleteDict', 'System\DictController@deleteDict');
    Route::post('dict/dictDataList', 'System\DictController@dictDataList');
    Route::post('dict/addDictData', 'System\DictController@addDictData');
    Route::post('dict/editDictData', 'System\DictController@editDictData');
    Route::post('dict/deleteDictData', 'System\DictController@deleteDictData');

    // 菜单管理
    Route::get('menu/getmenus', 'System\MenuController@getMenus');
    Route::get('menu/getrouter', 'System\MenuController@getRouter');
    Route::get('menu/menuselecter', 'System\MenuController@getMenuSelecter');
    Route::post('menu/menutree', 'System\MenuController@getMenuTree');
    Route::post('menu/add', 'System\MenuController@add');
    Route::post('menu/addMenu', 'System\MenuController@addMenu');
    Route::post('menu/editMenu', 'System\MenuController@editMenu');
    Route::post('menu/deleteMenu', 'System\MenuController@deleteMenu');

    // 权限管理
    Route::post('role/deleteRoleData', 'System\RoleController@deleteRoleData');
    Route::get('role/roles', 'System\RoleController@getRoles');
    Route::post('role/add', 'System\RoleController@add');
    Route::post('role/edit', 'System\RoleController@edit');
    Route::post('role/setDefaultRole', 'System\RoleController@setDefaultRole');
    Route::post('role/setrolemenus', 'System\RoleController@setRoleMenus');
    Route::post('role/getDepartmentTree', 'System\RoleController@getDepartmentTree');
    Route::post('role/editRoleDep', 'System\RoleController@editRoleDep');

    // 操作日志
    Route::post('log/getOperationLogs', 'Logs\LogController@getOperationLogs');

    // 上传文件
    Route::post('upload/file', 'Upload\UploadController@uploadFile');

    // 人员管理
    Route::post('personnel', 'Personnel\PersonnelController@store');
    Route::post('personnelList', 'Personnel\PersonnelController@personnelList');
    Route::delete('personnel', 'Personnel\PersonnelController@destroy');
    Route::get('personnel/{id}', 'Personnel\PersonnelController@edit');
    Route::put('personnel/{id}', 'Personnel\PersonnelController@update');
    Route::post('importAdmin', 'Personnel\PersonnelController@importAdmin');
    Route::post('importDriver', 'Personnel\PersonnelController@importDriver');

    // 车辆管理
    Route::post('bus', 'Bus\BusController@store');
    Route::post('busList', 'Bus\BusController@busList');
    Route::delete('bus', 'Bus\BusController@destroy');
    Route::get('bus/{id}', 'Bus\BusController@edit');
    Route::put('bus/{id}', 'Bus\BusController@update');
    Route::get('licensePlateNumList', 'Bus\BusController@getLicensePlateNumList');
    Route::post('importBus', 'Bus\BusController@importBus');

    // 收入记录
    Route::post('importCard', 'Income\IncomeController@importCard');
    Route::post('incomeCardList', 'Income\IncomeController@incomeCardList');
    Route::post('importPost', 'Income\IncomeController@importPost');
    Route::post('incomePostList', 'Income\IncomeController@incomePostList');

    Route::post('importCash', 'Income\IncomeCashController@importCash');
    Route::post('incomeCashList', 'Income\IncomeCashController@incomeCashList');
    Route::post('incomeCash', 'Income\IncomeCashController@storeCash');
    Route::put('incomeCash/{id}', 'Income\IncomeCashController@updateCash');
    Route::delete('incomeCash', 'Income\IncomeCashController@destroyCash');

    Route::post('incomeCharteredCarList', 'Income\IncomeCharteredCarController@incomeCharteredCarList');
    Route::post('incomeCharteredCar', 'Income\IncomeCharteredCarController@storeCharteredCar');
    Route::put('incomeCharteredCar/{id}', 'Income\IncomeCharteredCarController@updateCharteredCar');
    Route::delete('incomeCharteredCar', 'Income\IncomeCharteredCarController@destroyCharteredCar');

    Route::post('incomeCharteredCarFixedList', 'Income\IncomeCharteredCarFixedController@incomeCharteredCarFixedList');
    Route::post('incomeCharteredCarFixed', 'Income\IncomeCharteredCarFixedController@storeCharteredCarFixed');
    Route::put('incomeCharteredCarFixed/{id}', 'Income\IncomeCharteredCarFixedController@updateCharteredCarFixed');
    Route::delete('incomeCharteredCarFixed', 'Income\IncomeCharteredCarFixedController@destroyCharteredCarFixed');

    Route::post('attendanceList', 'Attendance\AttendanceController@attendanceList');
    Route::post('attendance', 'Attendance\AttendanceController@storeAttendance');
    Route::put('attendance/{id}', 'Attendance\AttendanceController@updateAttendance');
    Route::delete('attendance', 'Attendance\AttendanceController@destroyAttendance');
    Route::post('importAttendance', 'Attendance\AttendanceController@importAttendance');

    // 充气记录
    Route::post('importGas', 'Gas\GasController@importGas');
    Route::post('gasList', 'Gas\GasController@gasList');
    Route::post('importGasYh', 'Gas\GasYhController@importGasYh');
    Route::post('gasYhList', 'Gas\GasYhController@gasYhList');
    Route::post('importGasYhCheck', 'Gas\GasYhCheckController@importGasYhCheck');
    Route::post('gasYhCheckList', 'Gas\GasYhCheckController@gasYhCheckList');

    // 充电记录
    Route::post('importCharge', 'Charge\ChargeController@importCharge');
    Route::post('chargeList', 'Charge\ChargeController@chargeList');

    // 充电统计
    Route::post('chargeStatiList', 'Charge\ChargeStatiController@chargeStatiList');
    Route::get('exportChargeStati', 'Charge\ChargeStatiController@exportChargeStati');
    Route::post('incomeStatiList', 'Charge\ChargeStatiController@incomeStatiList');
    Route::get('exportIncomeStati', 'Charge\ChargeStatiController@exportIncomeStati');
    Route::post('kmStatiList', 'Charge\ChargeStatiController@kmStatiList');
    Route::get('exportKmStati', 'Charge\ChargeStatiController@exportKmStati');
    Route::post('yhStatiList', 'Charge\ChargeStatiController@yhStatiList');
    Route::get('exportYhGasStati', 'Charge\ChargeStatiController@exportYhGasStati');
    Route::post('skStatiList', 'Charge\ChargeStatiController@skStatiList');
    Route::get('exportSkGasStati', 'Charge\ChargeStatiController@exportSkGasStati');
    Route::post('yhAmtStatiList', 'Charge\ChargeStatiController@yhAmtStatiList');
    Route::get('exportYhAmtGasStati', 'Charge\ChargeStatiController@exportYhAmtGasStati');
    // 里程记录
    Route::post('importMileage', 'Mileage\MileageController@importMileage');
    Route::post('mileageList', 'Mileage\MileageController@mileageList');

    // 保险管理
    Route::post('insureList', 'Insure\InsureController@insureList');
    Route::post('addInsure', 'Insure\InsureController@addInsure');
    Route::post('editInsure', 'Insure\InsureController@editInsure');
    Route::post('dictData', 'Insure\InsureController@dictData');
    Route::post('insureInfo', 'Insure\InsureController@insureInfo');
    Route::delete('delInsure', 'Insure\InsureController@delInsure');
    Route::post('importInsure', 'Insure\InsureController@importInsure');

    // 二级维护
    Route::post('twoLevelMainteList', 'TwoLevelMainte\TwoLevelMainteController@twoLevelMainteList');
    Route::post('addTwoLevelMainte', 'TwoLevelMainte\TwoLevelMainteController@addTwoLevelMainte');
    Route::post('editTwoLevelMainte', 'TwoLevelMainte\TwoLevelMainteController@editTwoLevelMainte');
    Route::post('twoLevelMainteInfo', 'TwoLevelMainte\TwoLevelMainteController@twoLevelMainteInfo');
    Route::delete('delTwoLevelMainte', 'TwoLevelMainte\TwoLevelMainteController@delTwoLevelMainte');

    // 物资库存
    Route::post('lineBusNum', 'Material\MaterialController@lineBusNum');
    Route::post('workflowData', 'Material\MaterialController@workflowData');
    Route::post('materialList', 'Material\MaterialController@materialList');
    Route::post('addMaterial', 'Material\MaterialController@addMaterial');
    Route::post('editMaterial', 'Material\MaterialController@editMaterial');
    Route::post('materialInfo', 'Material\MaterialController@materialInfo');
    Route::post('delMaterial', 'Material\MaterialController@delMaterial');
    Route::post('materialStatiList', 'Material\MaterialController@materialStatiList');
    Route::get('exportMaterialStati', 'Material\MaterialController@exportMaterialStati');
    //入库
    Route::post('putMaterialList', 'Material\MaterialController@putMaterialList');
    Route::post('addPutMaterial', 'Material\MaterialController@addPutMaterial');
    Route::post('editPutMaterial', 'Material\MaterialController@editPutMaterial');
    Route::post('editMaterialDetail', 'Material\MaterialController@editMaterialDetail');
    Route::post('editAddPutMaterialDetail', 'Material\MaterialController@editAddPutMaterialDetail');
    Route::delete('editDelPutMaterialDetail', 'Material\MaterialController@editDelPutMaterialDetail');
    Route::post('putMaterialInfo', 'Material\MaterialController@putMaterialInfo');
    Route::delete('delPutMaterial', 'Material\MaterialController@delPutMaterial');
    Route::post('toAuditPut', 'Material\MaterialController@toAuditPut');

    //出入库详情
    Route::post('addMaterialDetail', 'Material\MaterialController@addMaterialDetail');
    //出库
    Route::post('outMaterialList', 'Material\MaterialController@outMaterialList');
    Route::post('addOutMaterial', 'Material\MaterialController@addOutMaterial');
    Route::post('editOutMaterial', 'Material\MaterialController@editOutMaterial');
    Route::post('outMaterialInfo', 'Material\MaterialController@outMaterialInfo');
    Route::delete('delOutMaterial', 'Material\MaterialController@delOutMaterial');
    Route::post('editOutMaterialDetail', 'Material\MaterialController@editOutMaterialDetail');
    Route::post('editAddOutMaterialDetail', 'Material\MaterialController@editAddOutMaterialDetail');
    Route::delete('editDelOutMaterialDetail', 'Material\MaterialController@editDelOutMaterialDetail');
    Route::post('toAuditOut', 'Material\MaterialController@toAuditOut');
    Route::post('outMaterialStatiList', 'Material\MaterialController@outMaterialStatiList');
    Route::get('exportOutMaterialStati', 'Material\MaterialController@exportOutMaterialStati');
    Route::get('exportOutMaterial', 'Material\MaterialController@exportOutMaterial');
    //轮胎出库
    Route::post('tyreOutMaterialList', 'Material\MaterialController@tyreOutMaterialList');
    Route::post('addTyreOutMaterial', 'Material\MaterialController@addTyreOutMaterial');
    Route::post('editTyreOutMaterial', 'Material\MaterialController@editTyreOutMaterial');
    Route::post('tyreOutMaterialInfo', 'Material\MaterialController@tyreOutMaterialInfo');
    Route::delete('delTyreOutMaterial', 'Material\MaterialController@delTyreOutMaterial');
    Route::post('editTyreOutMaterialDetail', 'Material\MaterialController@editTyreOutMaterialDetail');
    Route::post('editAddTyreOutMaterialDetail', 'Material\MaterialController@editAddTyreOutMaterialDetail');
    Route::delete('editDelTyreOutMaterialDetail', 'Material\MaterialController@editDelTyreOutMaterialDetail');
    Route::post('toAuditTyreOut', 'Material\MaterialController@toAuditTyreOut');
    Route::post('tyreOutMaterialStatiList', 'Material\MaterialController@tyreOutMaterialStatiList');
    Route::get('exportTyreOutMaterialStati', 'Material\MaterialController@exportTyreOutMaterialStati');
    Route::get('exportTyreOutMaterial', 'Material\MaterialController@exportTyreOutMaterial');

    // 维修申请
    Route::post('applyList', 'MaintainRepair\MrApplyController@applyList');
    Route::get('applyList/{id}', 'MaintainRepair\MrApplyController@editApply');
    Route::post('mrApply', 'MaintainRepair\MrApplyController@storeApply');
    Route::put('mrApply/{id}', 'MaintainRepair\MrApplyController@updateApply');
    Route::delete('mrApply', 'MaintainRepair\MrApplyController@destroyApply');
    Route::post('mrApply/audit', 'MaintainRepair\MrApplyController@audit');

    // 维修费用
    Route::post('costList', 'MaintainRepair\MrCostController@costList');
    Route::post('mrCost', 'MaintainRepair\MrCostController@storeCost');
    Route::put('mrCost/{id}', 'MaintainRepair\MrCostController@updateCost');
    Route::delete('mrCost', 'MaintainRepair\MrCostController@destroyCost');

    //任务工资
    Route::post('taskDalary', 'TaskSalary\TaskSalaryController@taskDalary');
    Route::post('taskDalaryList', 'TaskSalary\TaskSalaryController@taskDalaryList');
    Route::post('editTaskDalary', 'TaskSalary\TaskSalaryController@editTaskDalary');
    Route::post('taskDalaryInfo', 'TaskSalary\TaskSalaryController@taskDalaryInfo');
    Route::post('sortTaskDalary', 'TaskSalary\TaskSalaryController@sortTaskDalary');
    Route::get('exportTaskDalary', 'TaskSalary\TaskSalaryController@exportTaskDalary');

    //任务工资排名
    Route::post('taskRank', 'TaskSalary\TaskSalaryController@taskRank');
    Route::post('taskRankList', 'TaskSalary\TaskSalaryController@taskRankList');
    Route::post('editTaskRank', 'TaskSalary\TaskSalaryController@editTaskRank');
    Route::post('taskRankInfo', 'TaskSalary\TaskSalaryController@taskRankInfo');
    Route::get('exportTaskRank', 'TaskSalary\TaskSalaryController@exportTaskRank');

    //充电车任务工资
    Route::post('eleTaskDalary', 'TaskSalary\EleTaskSalaryController@eleTaskDalary');
    Route::post('eleTaskDalaryList', 'TaskSalary\EleTaskSalaryController@eleTaskDalaryList');
    Route::post('editEleTaskDalary', 'TaskSalary\EleTaskSalaryController@editEleTaskDalary');
    Route::post('eleTaskDalaryInfo', 'TaskSalary\EleTaskSalaryController@eleTaskDalaryInfo');
    Route::post('sortEleTaskDalary', 'TaskSalary\EleTaskSalaryController@sortEleTaskDalary');
    Route::get('exportEleTaskDalary', 'TaskSalary\EleTaskSalaryController@exportEleTaskDalary');

    //充电车任务工资排名
    Route::post('eleTaskRank', 'TaskSalary\EleTaskSalaryController@eleTaskRank');
    Route::post('eleTaskRankList', 'TaskSalary\EleTaskSalaryController@eleTaskRankList');
    Route::post('editEleTaskRank', 'TaskSalary\EleTaskSalaryController@editEleTaskRank');
    Route::post('eleTaskRankInfo', 'TaskSalary\EleTaskSalaryController@eleTaskRankInfo');
    Route::get('exportEleTaskRank', 'TaskSalary\EleTaskSalaryController@exportEleTaskRank');

    //单车线路均值汇总表
    Route::post('lineTaskDalaryList', 'TaskSalary\LineAverTaskSalaryController@lineTaskDalaryList');
    Route::get('exportLineTaskDalary', 'TaskSalary\LineAverTaskSalaryController@exportLineTaskDalary');

    //单车核算
    Route::post('vehicleAccounting', 'VehicleAccounting\VehicleAccountingController@vehicleAccounting');
    Route::get('exportVehicleAccounting', 'VehicleAccounting\VehicleAccountingController@exportVehicleAccounting');

    //考勤统计
    Route::post('attendanceAccounting', 'Attendance\AttendanceController@attendanceAccounting');
    Route::post('attendanceAccountingDate', 'Attendance\AttendanceController@attendanceAccountingDate');
    Route::post('attendanceAccountingData', 'Attendance\AttendanceController@attendanceAccountingData');
    Route::post('attendanceAccountingInfo', 'Attendance\AttendanceController@attendanceAccountingInfo');
    Route::post('editAttendanceAccounting', 'Attendance\AttendanceController@editAttendanceAccounting');
    Route::get('exportAttendanceAccounting', 'Attendance\AttendanceController@exportAttendanceAccounting');

    // 事故
    Route::post('accidentList', 'Accident\AccidentController@accidentList');
    Route::post('accident', 'Accident\AccidentController@storeAccident');
    Route::put('accident/{id}', 'Accident\AccidentController@updateAccident');
    Route::delete('accident', 'Accident\AccidentController@destroyAccident');

    // 包车明细管理
    Route::post('charterBusList', 'CharterBus\CharterBusController@charterBusList');
    Route::post('addCharterBus', 'CharterBus\CharterBusController@addCharterBus');
    Route::post('editCharterBus', 'CharterBus\CharterBusController@editCharterBus');
    Route::post('charterBusInfo', 'CharterBus\CharterBusController@charterBusInfo');
    Route::delete('delCharterBus', 'CharterBus\CharterBusController@delCharterBus');
    Route::post('importCharterBus', 'CharterBus\CharterBusController@importCharterBus');

    // 工资报表
    Route::post('leaderPayList', 'Pay\LeaderPayController@leaderPayList');
    Route::get('exportLeaderPay', 'Pay\LeaderPayController@exportLeaderPay');
    Route::post('adminPayList', 'Pay\AdminPayController@adminPayList');
    Route::get('exportAdminPay', 'Pay\AdminPayController@exportAdminPay');
    Route::post('driverPayList', 'Pay\DriverPayController@driverPayList');
    Route::get('exportDriverPay', 'Pay\DriverPayController@exportDriverPay');
});
