<?php

namespace App\Http\Controllers\Integral;


use App\Http\Controllers\Controller;
use App\Models\Role;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Dict;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Departments;
use App\Models\Menu;

class DingController extends Controller
{
    public $seeIds;
    public $office;
    public $projectsCache;
    public $projectPlanCache;
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
    // curl
    public function postCurl($url,$data,$type) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if($type=='post'){
            curl_setopt($ch, CURLOPT_POST, TRUE);
            //设置post数据
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        return $json;
    }
    //获取钉钉token
    public function getToken(){
        $appKey=env("Ding_App_Key");
        $appSecret=env("Ding_App_Secret");
        $url='https://oapi.dingtalk.com/gettoken?appkey='.$appKey.'&appsecret='.$appSecret;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        $arr=json_decode($json,true);
        Cache::put('dingAccessToken', $arr['access_token'], 7200);
        
        dd($arr);
    }
    //获取钉钉用户信息
    public function userId(Request $request){
        $data = $request->all();
        $appKey=env("Ding_App_Key");
        $appSecret=env("Ding_App_Secret");
        $accessToken=Cache::get('dingAccessToken');
        if(!$accessToken){
            $this->getToken();
            $accessToken=Cache::get('dingAccessToken');
        }
        $user_id_url='https://oapi.dingtalk.com/user/getuserinfo?access_token='.$accessToken.'&code='.$data['code'];
        $user_ids=$this->postCurl($user_id_url,[],'get');
        $user_id=json_decode($user_ids,true);
        var_dump($user_ids."***".$user_id);
        $url='https://oapi.dingtalk.com/user/get?access_token='.$accessToken.'&userid='.$user_id['userid'];
        $json=$this->postCurl($url,[],'get');
        $arr=json_decode($json,true);
        Cache::put('userid', $arr['userid'], 7200);
        $ids = DB::table('users')->where('phone', $arr['mobile'])->value('id');
        if($ids){
            $result = DB::table('users')->where('phone', $arr['mobile'])->update(['ding_user_id'=>$arr['userid']]);
        }
        return response()->json(['result' => $arr,'ids'=>$ids?$ids:false], 200);
    }
    /**
     * 获取项目库表单中的数据字典数据多个
     *
     * @param Request $request
     * @return array
     */
    public function dictData(Request $request)
    {
        $nameArr = $request->input("dictName");
        $result = Dict::getOptionsByNameArr($nameArr);
        return response()->json(['result' => $result], 200);
    }
    //获取销售详情
    public function salesData(Request $request)
    {   
        $params =  $request->input();

        $data = DB::table('integral');
        $data=$data->where('id',$params['id'])->first();

        $project_type_v = Dict::getOptionsArrByName('产品类型价值');
        $project_type_d = Dict::getOptionsArrByName('产品类型发展');
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
        if(isset($data['is_new_user'])&&isset($data['project_type'])){
            if($data['is_new_user']===0){
                $data['project_type']=$project_type_v[$data['project_type']];
            }else{
                $data['project_type']=$project_type_d[$data['project_type']];
            }
            $data['is_new_user'] = $is_new_user[$data['is_new_user']];
        }
        if(isset($data['business_type'])){
            $data['business_type'] = $business_type[$data['business_type']];
        }
        if(isset($data['terminal_type'])){
            $data['terminal_type'] = $terminal_type[$data['terminal_type']];
        }
        $set_meal_arr=json_decode($data['set_meal'],true);
        $set_meal_info='';
        if(isset($set_meal_arr['meal']['meal_type'])&&isset($set_meal_arr['meal']['meal'])){
            if($set_meal_arr['meal']['meal_type']===0){
                $meal_type=$set_meal_0[$set_meal_arr['meal']['meal']];
            }elseif($set_meal_arr['meal']['meal_type']===1){
                $meal_type=$set_meal_1[$set_meal_arr['meal']['meal']];
            }elseif($set_meal_arr['meal']['meal_type']===2){
                $meal_type=$set_meal_2[$set_meal_arr['meal']['meal']];
            } 
            $set_meal_info='套餐：'.$meal_type;
        }
        if(isset($set_meal_arr['up_meal'])){
            foreach($set_meal_arr['up_meal'] as $v){
                if($v['meal_type']===0&&isset($v['meal'])){
                    $up_meal_type=$set_up_meal_0[$v['meal']];
                }elseif($v['meal_type']===1&&isset($v['meal'])){
                    $up_meal_type=$set_up_meal_1[$v['meal']];
                }elseif($v['meal_type']===2&&isset($v['meal'])){
                    $up_meal_type=$set_up_meal_2[$v['meal']];
                }elseif($v['meal_type']===3&&isset($v['meal'])){
                    $up_meal_type=$set_up_meal_3[$v['meal']];
                }else{
                    $up_meal_type="";
                }
                $set_meal_info=$set_meal_info.'、'.$up_meal_type;
            }
        }
        $data['set_meal'] = $set_meal_info;
        $applicant = DB::table('users')->where('id',$data['applicant'])->value('name');
        $data['applicant'] = $applicant;
        return response()->json(['result' => $data], 200);
    }
    //销售数据列表
    public function salesDataList(Request $request)
    {   
        $params =  $request->input();

        $result = DB::table('integral');
        if(isset($params['date_time'])){
            $result->where('date_time',$params['date_time']);
        }
        if (isset($params['pageNumber']) && isset($params['pageSize'])) {
            $data = $result
                ->limit($params['pageSize'])
                ->offset(($params['pageNumber'] - 1) * $params['pageSize']);
        }else{
            $data=$result;
        }
        $data=$data->get()->toArray();
        $count = $result->count();
        $total_integral=0;
        $project_type_v = Dict::getOptionsArrByName('产品类型价值');
        $project_type_d = Dict::getOptionsArrByName('产品类型发展');
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
                if($row['project_type']>3){
                    $data[$k]['project_type']=$project_type_v[$row['project_type']];
                }
            }else{
                if($row['project_type']<4){
                    $data[$k]['project_type']=$project_type_d[$row['project_type']];
                }
            }
            if(isset($row['business_type'])){
                $data[$k]['business_type'] = $business_type[$row['business_type']];
            }
            if(isset($row['terminal_type'])){
                $data[$k]['terminal_type'] = $terminal_type[$row['terminal_type']];
            }
            $set_meal_arr=json_decode($row['set_meal'],true);
            $set_meal_info='';
            if(isset($set_meal_arr['meal']['meal_type'])&&isset($set_meal_arr['meal']['meal'])){
                if($set_meal_arr['meal']['meal_type']===0){
                    $meal_type=$set_meal_0[$set_meal_arr['meal']['meal']];
                }elseif($set_meal_arr['meal']['meal_type']===1){
                    $meal_type=$set_meal_1[$set_meal_arr['meal']['meal']];
                }elseif($set_meal_arr['meal']['meal_type']===2){
                    $meal_type=$set_meal_2[$set_meal_arr['meal']['meal']];
                }
                $set_meal_info='套餐：'.$meal_type;
            }
            if(isset($set_meal_arr['up_meal'])){
                foreach($set_meal_arr['up_meal'] as $v){
                    if($v['meal_type']===0&&isset($v['meal'])){
                        $up_meal_type=$set_up_meal_0[$v['meal']];
                    }elseif($v['meal_type']===1&&isset($v['meal'])){
                        $up_meal_type=$set_up_meal_1[$v['meal']];
                    }elseif($v['meal_type']===2&&isset($v['meal'])){
                        $up_meal_type=$set_up_meal_2[$v['meal']];
                    }elseif($v['meal_type' ]===3&&isset($v['meal'])){
                        $up_meal_type=$set_up_meal_3[$v['meal']];
                    }else{
                        $up_meal_type="";
                    }
                    $set_meal_info=$set_meal_info.'、'.$up_meal_type;
                }
            }
            $data[$k]['set_meal'] = $set_meal_info;
            $applicant = DB::table('users')->where('id',$row['applicant'])->value('name');
            $data[$k]['applicant'] = $applicant;
            $total_integral=$total_integral+$row['int_num'];
        }

        return response()->json(['result' => $data, 'total' => $count,'total_integral'=>$total_integral], 200);
    }
    //销售数据填报
    public function salesDataAdd(Request $request)
    {
        $params =  $request->input();
        if(isset($params['meal_info'])){
            $params['set_meal']=json_encode($params['meal_info']);
        }
        unset($params['meal_info']);
        // $users=$this->user->id;
        $users=1;
        // $area=DB::table('iba_system_department')->where('id',$this->department_id)->value('title');
        $params['area'] = '安康市';
        $params['applicant'] = $users;
        $params['date_time'] = date('Y-m-d');
        $params['created_at']=date('Y-m-d H:i:s');
        $id = DB::table('integral')->insertGetId($params);

        $result = $id ? true : false;

        return response()->json(['result' => $result], 200);
    }
    
    //个人资料
    public function userInfo(Request $request)
    {
        $params =  $request->input();
        $result=[];
        $result['name']='汪光军';
        $result['department']='岚皋县翼泽手机销售店';
        $result['mobile']='17719693261';
        return response()->json(['result' => $result], 200);
    }
    //统计
    public function importIntegral(Request $request)
    {
        //
        $params =  $request->input();
        $development_total=0;
        $value_total=0;
        $development=DB::table('import_development_integral')
                            ->selectRaw('sum(u_single_move) as u_single_move_total,sum(u_single_wifi) as u_single_wifi_total,sum(u_fuse) as u_fuse_total,sum(u_gover_products) as u_gover_products_total,date_time')
                            ->groupBy('date_time')
                            ->orderBy('id','desc')
                            ->get()->toArray();
        $value=DB::table('import_value_integral')
                            ->selectRaw('sum(stock_v_up) as stock_v_up_total,sum(stock_contract) as stock_contract_total,sum(stock_tenure) as stock_tenure_total,date_time')
                            ->groupBy('date_time')
                            ->orderBy('id','desc')
                            ->get()->toArray();
        foreach($development as $k=>$v){
            $development[$k]['development_date_total']=$v['u_single_move_total'] + $v['u_single_wifi_total'] + $v['u_fuse_total'] + $v['u_gover_products_total'];
            $development_total = $development_total + $v['u_single_move_total'] + $v['u_single_wifi_total'] + $v['u_fuse_total'] + $v['u_gover_products_total'];
            unset($development[$k]['u_single_move_total'],$development[$k]['u_single_wifi_total'],$development[$k]['u_fuse_total'],$development[$k]['u_gover_products_total']);
        }
        foreach($value as $i=>$v){
            $value[$i]['value_date_total']=$v['stock_v_up_total'] + $v['stock_contract_total'] + $v['stock_tenure_total'];
            $value_total = $value_total + $v['stock_v_up_total'] + $v['stock_contract_total'] + $v['stock_tenure_total'];
            unset($value[$i]['stock_v_up_total'],$value[$i]['stock_contract_total'],$value[$i]['stock_tenure_total']);
        }
        $result=['development'=>$development,'value'=>$value,'development_total'=>$development_total,'value_total'=>$value_total];
        return response()->json(['result' => $result], 200);
    }
}
                     