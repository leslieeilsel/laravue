<?php

namespace App\Http\Controllers\Integral;


use App\Http\Controllers\Controller;
use App\Models\Role;
use App\User;
use Illuminate\Http\JsonResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectEarlyWarning;
use Illuminate\Support\Facades\Storage;
use App\Models\Dict;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Departments;
use App\Models\Menu;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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

        $data = DB::table('integral');
        if (isset($params['pageNumber']) && isset($params['pageSize'])) {
            $data = $data
                ->limit($params['pageSize'])
                ->offset(($params['pageNumber'] - 1) * $params['pageSize']);
        }
        $data=$data->where('is_new_user',0)->orWhereIn('project_type',[7,8,9])->get()->toArray();
        $count = DB::table('integral')->count();
        $project_type_v = Dict::getOptionsArrByName('产品类型(价值)');
        $project_type_vs=[];
        $p=4;
        foreach($project_type_v as $k=>$v){
            $project_type_vs[$k+$p]=$v;
        }
        $project_type_d = Dict::getOptionsArrByName('产品类型(发展)');
        $business_type = Dict::getOptionsArrByName('业务类型');
        $is_new_user = Dict::getOptionsArrByName('是否新用户');
        $terminal_type = Dict::getOptionsArrByName('终端类型');
        $set_meal = Dict::getOptionsArrByName('套餐');
        $set_meal_0 = Dict::getOptionsArrByName('融合套餐');
        $set_meal_1 = Dict::getOptionsArrByName('单卡套餐');
        $set_meal_2 = Dict::getOptionsArrByName('智慧企业套餐');
        $set_up_meal = Dict::getOptionsArrByName('升级套餐');
        $set_up_meal_0 = Dict::getOptionsArrByName('智慧家庭升级包');
        $set_up_meal_1 = Dict::getOptionsArrByName('5G升级包');
        $set_up_meal_2 = Dict::getOptionsArrByName('加第二路宽带');
        $set_up_meal_3 = Dict::getOptionsArrByName('加卡');
        foreach ($data as $k => $row) {
            $data[$k]['is_new_user'] = $is_new_user[$row['is_new_user']];
            if($row['is_new_user']===0){
                $data[$k]['project_type']=$project_type_vs[$row['project_type']];
            }else{
                $data[$k]['project_type']=$project_type_d[$row['project_type']];
            }
            $data[$k]['business_type'] = $business_type[$row['business_type']];
            $data[$k]['terminal_type'] = $terminal_type[$row['terminal_type']];

            $set_meal_arr=json_decode($row['set_meal'],true);
            $set_meal_info='';
            if($set_meal_arr['meal']['meal_type']===0){
                $meal_type=$set_meal_0[$set_meal_arr['meal']['meal']];
            }elseif($set_meal_arr['meal']['meal_type']===1){
                $meal_type=$set_meal_1[$set_meal_arr['meal']['meal']];
            }elseif($set_meal_arr['meal']['meal_type']===2){
                $meal_type=$set_meal_2[$set_meal_arr['meal']['meal']];
            }
            $set_meal_info='套餐：'.$meal_type;
            foreach($set_meal_arr['up_meal'] as $v){
                if($v['meal_type']===0){
                    $up_meal_type=$set_up_meal_0[$v['meal']];
                }elseif($v['meal_type']===1){
                    $up_meal_type=$set_up_meal_1[$v['meal']];
                }elseif($v['meal_type']===2){
                    $up_meal_type=$set_up_meal_2[$v['meal']];
                }elseif($v['meal_type']===3){
                    $up_meal_type=$set_up_meal_3[$v['meal']];
                }
                $set_meal_info=$set_meal_info.'、'.$up_meal_type;
            }
            $data[$k]['set_meal'] = $set_meal_info;
        }

        return response()->json(['result' => $data, 'total' => $count], 200);
    }
    //销售数据列表
    public function salesDataList(Request $request)
    {   
        $params =  $request->input();

        $data = DB::table('integral');
        if (isset($params['pageNumber']) && isset($params['pageSize'])) {
            $data = $data
                ->limit($params['pageSize'])
                ->offset(($params['pageNumber'] - 1) * $params['pageSize']);
        }
        $data=$data->where('is_new_user',1)->orWhereIn('project_type',[4,5,6])->get()->toArray();
        $count = DB::table('integral')->count();
        $project_type_v = Dict::getOptionsArrByName('产品类型(价值)');
        $project_type_vs=[];
        $p=4;
        foreach($project_type_v as $k=>$v){
            $project_type_vs[$k+$p]=$v;
        }
        $project_type_d = Dict::getOptionsArrByName('产品类型(发展)');
        $business_type = Dict::getOptionsArrByName('业务类型');
        $is_new_user = Dict::getOptionsArrByName('是否新用户');
        $terminal_type = Dict::getOptionsArrByName('终端类型');
        $set_meal = Dict::getOptionsArrByName('套餐');
        $set_meal_0 = Dict::getOptionsArrByName('融合套餐');
        $set_meal_1 = Dict::getOptionsArrByName('单卡套餐');
        $set_meal_2 = Dict::getOptionsArrByName('智慧企业套餐');
        $set_up_meal = Dict::getOptionsArrByName('升级套餐');
        $set_up_meal_0 = Dict::getOptionsArrByName('智慧家庭升级包');
        $set_up_meal_1 = Dict::getOptionsArrByName('5G升级包');
        $set_up_meal_2 = Dict::getOptionsArrByName('加第二路宽带');
        $set_up_meal_3 = Dict::getOptionsArrByName('加卡');
        foreach ($data as $k => $row) {
            $data[$k]['is_new_user'] = $is_new_user[$row['is_new_user']];
            if($row['is_new_user']===0){
                $data[$k]['project_type']=$project_type_vs[$row['project_type']];
            }else{
                $data[$k]['project_type']=$project_type_d[$row['project_type']];
            }
            $data[$k]['business_type'] = $business_type[$row['business_type']];
            $data[$k]['terminal_type'] = $terminal_type[$row['terminal_type']];

            $set_meal_arr=json_decode($row['set_meal'],true);
            $set_meal_info='';
            if($set_meal_arr['meal']['meal_type']===0){
                $meal_type=$set_meal_0[$set_meal_arr['meal']['meal']];
            }elseif($set_meal_arr['meal']['meal_type']===1){
                $meal_type=$set_meal_1[$set_meal_arr['meal']['meal']];
            }elseif($set_meal_arr['meal']['meal_type']===2){
                $meal_type=$set_meal_2[$set_meal_arr['meal']['meal']];
            }
            $set_meal_info='套餐：'.$meal_type;
            foreach($set_meal_arr['up_meal'] as $v){
                if($v['meal_type']===0){
                    $up_meal_type=$set_up_meal_0[$v['meal']];
                }elseif($v['meal_type']===1){
                    $up_meal_type=$set_up_meal_1[$v['meal']];
                }elseif($v['meal_type']===2){
                    $up_meal_type=$set_up_meal_2[$v['meal']];
                }elseif($v['meal_type']===3){
                    $up_meal_type=$set_up_meal_3[$v['meal']];
                }
                $set_meal_info=$set_meal_info.'、'.$up_meal_type;
            }
            $data[$k]['set_meal'] = $set_meal_info;
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
        // $params['set_meal']="'".json_encode($params['meal_info'])."'";
        $params['set_meal']=json_encode($params['meal_info']);
        unset($params['meal'],$params['meal_info'],$params['meal_type'],$params['integral']);
        $params['date_time'] = date('Y-m-d', strtotime($params['date_time']));
        $id = DB::table('integral')->insertGetId($params);

        $result = $id ? true : false;

        return response()->json(['result' => $result], 200);
    }
    //活动计划列表
    public function activityPlan(Request $request)
    {   
        $params =  $request->input();

        $data = DB::table('activity_plan');
        if (isset($params['pageNumber']) && isset($params['pageSize'])) {
            $data = $data
                ->limit($params['pageSize'])
                ->offset(($params['pageNumber'] - 1) * $params['pageSize']);
        }
        $data=$data->get()->toArray();
        $count = DB::table('activity_plan')->count();

        return response()->json(['result' => $data, 'total' => $count], 200);
    }
    //活动计划填报
    public function activityPlanAdd(Request $request)
    {   
        $params =  $request->input();
        $params['plan_time'] = date('Y-m-d', strtotime($params['plan_time']));
        $id = DB::table('activity_plan')->insertGetId($params);

        $result = $id ? true : false;

        return response()->json(['result' => $result], 200);
    }
    //活动执行列表
    public function activityImplement(Request $request)
    {   
        $params =  $request->input();

        $data = DB::table('activity');
        if (isset($params['pageNumber']) && isset($params['pageSize'])) {
            $data = $data
                ->limit($params['pageSize'])
                ->offset(($params['pageNumber'] - 1) * $params['pageSize']);
        }
        $data=$data->get()->toArray();
        $count = DB::table('activity')->count();

        return response()->json(['result' => $data, 'total' => $count], 200);
    }
    //活动执行填报
    public function activityImplementAdd(Request $request)
    {   
        $params =  $request->input();
        $params['date_time'] = date('Y-m-d', strtotime($params['date_time']));
        $id = DB::table('activity')->insertGetId($params);

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
    /**
     * 上传
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadPic(Request $request)
    {
        $params = $request->all();
        $project_title = DB::table('supervise_service')->where('id', $params['id'])->value('title');
        $suffix = $params['pic']->getClientOriginalExtension();
        $path = Storage::putFileAs(
            'public/value/project-schedule/' . $project_title . '/' . $params['month'],
            $request->file('pic'),
            rand(1000000, time()) . '.' . $suffix
        );
        $path = 'storage/' . substr($path, 7);

        return response()->json(['result' => $path], 200);
    }
    //导入发展积分
    public function importIntegral(Request $request){
        $params = $request->all();
        $suffix = $params['import_file']->getClientOriginalExtension();
        $path = Storage::putFileAs(
            'public/value/sale-data',
            $request->file('import_file'),
            rand(1000000, time()) . '.' . $suffix
        );
        $path = 'storage/' . substr($path, 7);
        
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = IOFactory::load($path);// 载入excel表格
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // 总行数
        $highestColumn = $worksheet->getHighestColumn(); // 总列数
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        $data = [];
        for ($row = 3; $row <= $highestRow; $row++) { // 从第几行开始读取
            $data[]=$row;
            $row_data = [];
            for ($column = 1; $column <= $highestColumnIndex; $column++) {
                $row_data[] = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
            }
            $id = DB::table('import_development_integral')
                ->insertGetId([
                    'province'=>$row_data[0],'local_wifi'=>$row_data[1],
                    'three_wifi'=>$row_data[2],'obu'=>$row_data[3],
                    'area'=>$row_data[4],'six_wifi'=>$row_data[5],
                    'u_single_move'=>$row_data[8],'u_single_wifi'=>$row_data[9],
                    'u_fuse'=>$row_data[10],'u_gover_products'=>$row_data[11],
                    'date_time'=>date("Y-m-d",strtotime("-2 day"))
                    ]);
            $data[] = $row_data; //获取
        }
        return response()->json(['result' => true], 200);
    }
    //导入价值积分
    public function importValueIntegral(Request $request){
        $params = $request->all();
        $suffix = $params['import_file']->getClientOriginalExtension();
        $path = Storage::putFileAs(
            'public/value/value',
            $request->file('import_file'),
            rand(1000000, time()) . '.' . $suffix
        );
        $path = 'storage/' . substr($path, 7);
        
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = IOFactory::load($path);// 载入excel表格
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // 总行数
        $highestColumn = $worksheet->getHighestColumn(); // 总列数
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        $data = [];
        for ($row = 3; $row <= $highestRow; $row++) { // 从第几行开始读取
            $data[]=$row;
            $row_data = [];
            for ($column = 1; $column <= $highestColumnIndex; $column++) {
                $row_data[] = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
            }
            $id = DB::table('import_value_integral')
                ->insertGetId([
                    'province'=>$row_data[0],'local_wifi'=>$row_data[1],
                    'three_wifi'=>$row_data[2],'obu'=>$row_data[3],
                    'area'=>$row_data[4],'six_wifi'=>$row_data[5],
                    'stock_v_up'=>$row_data[7],'stock_contract'=>$row_data[8],
                    'stock_tenure'=>$row_data[9],'date_time'=>date("Y-m-d",strtotime("-2 day"))
                    ]);
            $data[] = $row_data; //获取
        }
        return response()->json(['result' => true], 200);
    }
    //获取组织架构
    public function departmentList(Request $request){
        return response()->json(['result' => $this->getDepartmentList()], 200);
    }
    
    public function getDepartmentList()
    {
        $departments = Departments::all()->toArray();
        $data = [];
        foreach ($departments as $k => $v) {
            if ($v['parent_id'] === 0) {
                $v['children'] = $this->getChild($v['id'], $departments);
                $v['key'] = $v['id'];
                $v['expand'] = true;
                $data[] = $v;
            }
        }

        return $data;
    }
    public function getChild($pid, $departments)
    {
        $tree = [];
        foreach ($departments as $k => $v) {
            if ($v['parent_id'] === $pid) {
                // 匹配子记录
                $v['children'] = $this->getChild($v['id'], $departments); // 递归获取子记录
                if ($v['children'] == null) {
                    unset($v['children']);                          // 如果子元素为空则unset()
                }
                $v['key'] = $v['id'];
                $v['expand'] = true;
                $tree[] = $v;
            }
        }

        return $tree;
    }
    //查询组织机构的详情
    public function departmentInfo(Request $request)
    {   
        $params =  $request->input();

        $result = DB::table('department_info')->where('department_id',$params['id'])->first();

        return response()->json(['result' => $result], 200);
    }
    //巡店的填报
    public function videoPatrolAdd(Request $request)
    {   
        $params =  $request->input();

        if(!isset($params['shop_state'])){
            $params['shop_state']=0;
        }
        if(!isset($params['desc'])){
            $params['desc']='';
        }
        $params['applicant']=$params['create_by'];
        $params['department_info_id']=$params['id'];
        $params['date_time']=date('Y-m-d');
        unset($params['title'],$params['sort'],$params['status'],$params['parent_id'],$params['description'],
        $params['create_by'],$params['update_by'],$params['created_at'],$params['updated_at'],
        $params['key'],$params['expand'],$params['nodeKey'],$params['selected'],$params['name'],
        $params['mobile'],$params['area'],$params['addr'],$params['id']);
        $c=$params;
        $id = DB::table('video_patrol')->insertGetId($params);

        $result = $id ? true : false;

        return response()->json(['result' => $result], 200);
    }
}
                     