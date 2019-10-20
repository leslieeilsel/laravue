<?php

namespace App\Http\Controllers\Integral;


use App\Http\Controllers\Controller;
use App\Models\Role;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectEarlyWarning;
use Illuminate\Support\Facades\Storage;
use App\Models\Dict;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Departments;
use App\Models\Menu;

class IntegralController extends Controller
{
    public $seeIds;
    public $office;
    public $cache;
    public function getSeeIds($userId)
    {
        if ($userId) {
            $userInfo = User::where('ding_user_id',$userId)->first();
            if($userInfo){
                $roleId = $userInfo['group_id'];
                $this->office = $userInfo['office'];
                $dataType = Role::where('id', $roleId)->first()->data_type;

                if ($dataType === 0) {
                    $userIds = User::all()->toArray();
                    $this->seeIds = array_column($userIds, 'id');
                }
                if ($dataType === 1) {
                    $departmentIds = DB::table('iba_role_department')->where('role_id', $roleId)->get()->toArray();
                    $departmentIds = array_column($departmentIds, 'department_id');
                    $userIds = User::whereIn('department_id', $departmentIds)->get()->toArray();
                    $this->seeIds = array_column($userIds, 'id');
                }
                if ($dataType === 2) { 
                    $this->seeIds = [$userInfo['id']];
                }
            }
        }
    }
    //获取价值积分列表
    public function valueIntegralList(Request $request)
    {   
        $params =  $request->input();

        $data = DB::table('value_integral');
        if (isset($params['pageNumber']) && isset($params['pageSize'])) {
            $data = $data
                ->limit($params['pageSize'])
                ->offset(($params['pageNumber'] - 1) * $params['pageSize']);
        }
        $data=$data->get()->toArray();
        $count = DB::table('value_integral')->count();

        return response()->json(['result' => $data, 'total' => $count], 200);
    }
    //销售数据列表
    public function salesDataList(Request $request)
    {   
        $params =  $request->input();

        $data = DB::table('sales_data');
        if (isset($params['pageNumber']) && isset($params['pageSize'])) {
            $data = $data
                ->limit($params['pageSize'])
                ->offset(($params['pageNumber'] - 1) * $params['pageSize']);
        }
        $data=$data->get()->toArray();
        $count = DB::table('sales_data')->count();
        $product_type = Dict::getOptionsArrByName('产品类型');
        $business_type = Dict::getOptionsArrByName('业务类型');
        $meal = Dict::getOptionsArrByName('套餐');
        $is_new_user = Dict::getOptionsArrByName('是否新用户');
        foreach ($data as $k => $row) {
            $data[$k]['product_type'] = $product_type[$row['product_type']];
            $data[$k]['business_type'] = $business_type[$row['business_type']];
            $data[$k]['meal'] = $meal[$row['meal']];
            $data[$k]['is_new_user'] = $is_new_user[$row['is_new_user']];
        }

        return response()->json(['result' => $data, 'total' => $count], 200);
    }
    //片区积分目标列表
    public function areaMeritsAimList(Request $request)
    {   
        $params =  $request->input();

        $data = DB::table('area_target');
        if (isset($params['pageNumber']) && isset($params['pageSize'])) {
            $data = $data
                ->limit($params['pageSize'])
                ->offset(($params['pageNumber'] - 1) * $params['pageSize']);
        }
        $data=$data->get()->toArray();
        $count = DB::table('area_target')->count();
        $product_type = Dict::getOptionsArrByName('产品类型');
        $business_type = Dict::getOptionsArrByName('业务类型');
        foreach ($data as $k => $row) {
            $data[$k]['product_type'] = $product_type[$row['product_type']];
            $data[$k]['business_type'] = $business_type[$row['business_type']];
        }

        return response()->json(['result' => $data, 'total' => $count], 200);
    }
    //销售数据填报
    public function salesDataAdd(Request $request)
    {   
        $params =  $request->input();

        $id = DB::table('sales_data')->insertGetId($params);

        $result = $id ? true : false;

        return response()->json(['result' => $result], 200);
    }
    //片区积分目标填报
    public function areaMeritsAimAdd(Request $request)
    {   
        $params =  $request->input();

        $id = DB::table('area_target')->insertGetId($params);

        $result = $id ? true : false;

        return response()->json(['result' => $result], 200);
    }
    
    /**
     * 获取项目库表单中的数据字典数据
     *
     * @param Request $request
     * @return array
     */
    public function dictData(Request $request)
    {
        $nameArr = $request->input('dictName');
        $result = Dict::getOptionsByNameArr($nameArr);

        return response()->json(['result' => $result], 200);
    }
}
                     