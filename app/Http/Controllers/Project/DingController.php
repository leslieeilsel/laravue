<?php

namespace App\Http\Controllers\Project;


use App\Http\Controllers\Controller;
use App\Models\Project\Projects;
use App\Models\Project\ProjectPlan;
use App\Models\Project\ProjectSchedule;
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
                    $this->seeIds = [$userId];
                }
            }
        }
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
        // dd($arr['access_token']);
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
        $url='https://oapi.dingtalk.com/user/get?access_token='.$accessToken.'&userid='.$user_id['userid'];
        $json=$this->postCurl($url,[],'get');
        $arr=json_decode($json,true);
        $result = DB::table('users')->where('phone', $arr['mobile'])->update(['ding_user_id'=>$arr['userid']]);
        return $json;
    }
    //发送消息
    public function userNotify(){
        $appKey=env("Ding_App_Key");
        $appSecret=env("Ding_App_Secret");
        $agent_id=env("Ding_Agent_Id");
        $accessToken=Cache::get('dingAccessToken');
        if(!$accessToken){
            $this->getToken();
            $accessToken=Cache::get('dingAccessToken');
        }
        $url='https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token='.$accessToken;
        
        $post_data=array(
            'agent_id'=>$agent_id,
            'userid_list'=>'0362614366942884',
            'msg'=>json_encode([
                "msgtype"=>"text",
                "text"=>["content"=>"张三的请假申请"]
            ])
        );
        $json=$this->postCurl($url,$post_data,'post');
        return $json;
    }
    public function sign(){
        $appSecret=env("Ding_App_Secret");
        $appId=env("Ding_App_Key");
        $time=$this->getMillisecond();
        $s = hash_hmac('sha256', $time , $appSecret, true);
        $signature = base64_encode($s);
        $urlencode_signature = urlencode($signature);
        return ['appId'=>$appId,'time'=>$time,'sign'=>$urlencode_signature];
        // return $urlencode_signature;
    }
    // 毫秒级时间戳
    public function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
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
    //获取项目信息
    public function getAuditedProjects()
    {
        $projects = Projects::where('is_audit', 1)->whereIn('user_id', [16])->get()->toArray();

        return response()->json(['result' => $projects], 200);
    }
    /**
     * 项目信息填报
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function projectProgress(Request $request)
    {
        $datas = $request->all();
        $data=$datas['form'];
        $user_id=$datas['userid'];
        unset($data['project_num'], $data['subject'], $data['build_start_at'], $data['build_end_at'],
            $data['total_investors'], $data['plan_investors'], $data['plan_img_progress'], $data['acc_complete'],
            $data['plan_build_start_at']);
        $data['month'] = date('Y-m', strtotime($data['month']));
        $year = (int) date('Y', strtotime($data['month']));
        $month = (int) date('m', strtotime($data['month']));
        $plan_id = DB::table('iba_project_plan')->where('project_id', $data['project_id'])->where('date', $year)->value('id');
        $plan_month_id = DB::table('iba_project_plan')->where('project_id', $data['project_id'])->where('parent_id', $plan_id)->where('date', $month)->value('id');

        // $data['build_start_at'] = date('Y-m', strtotime($data['build_start_at']));
        // $data['build_end_at'] = date('Y-m', strtotime($data['build_end_at']));
        // if ($data['plan_build_start_at']) {
        //     $data['plan_build_start_at'] = date('Y-m', strtotime($data['plan_build_start_at']));
        // }
        $project_title = Projects::where('id', $data['project_id'])->value('title');
        $path = 'storage/project/project-schedule/' . $project_title . '/' . $data['month'];
        if ($data['img_progress_pic']) {
            $imgProgressPic = explode(',', $data['img_progress_pic']);
            $handler = opendir($path);
            while (($filename = readdir($handler)) !== false) {
                if ($filename != "." && $filename != "..") {
                    if (!in_array($path . '/' . $filename, $imgProgressPic)) {
                        unlink($path . '/' . $filename);
                    }
                }
            }
        } else {
            if (file_exists($path)) {
                $handler = opendir($path);
                while (($filename = readdir($handler)) !== false) {
                    if ($filename != "." && $filename != "..") {
                        unlink($path . '/' . $filename);
                    }
                }
            }
        }
        $data['is_audit'] = 4;
        $data['plan_id'] = $plan_month_id;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['user_id'] = DB::table('users')->where('ding_user_id', $user_id)->value('id');
        $schedule_id = DB::table('iba_project_schedule')->insertGetId($data);
        $result = $schedule_id;
        return response()->json(['result' => $result], 200);
    }
    /**
     * 获取所有项目信息
     *
     * @param $params
     * @return mixed
     */
    public function allProjects($params)
    {
        $query = new Projects;
        if (isset($params['title'])) {
            $query = $query->where('title', 'like', '%' . $params['title'] . '%');
        }
        if (isset($params['subject'])) {
            $query = $query->where('subject', 'like', '%' . $params['subject'] . '%');
        }
        if (isset($params['unit'])) {
            $query = $query->where('unit', 'like', '%' . $params['unit'] . '%');
        }
        if (isset($params['num'])) {
            $query = $query->where('num', $params['num']);
        }
        if (isset($params['type'])) {
            if ($params['type'] != -1) {
                $query = $query->where('type', $params['type']);
            }
        }
        if (isset($params['build_type'])) {
            if ($params['build_type'] != -1) {
                $query = $query->where('build_type', $params['build_type']);
            }
        }
        if (isset($params['money_from'])) {
            if ($params['money_from'] != -1) {
                $query = $query->where('money_from', $params['money_from']);
            }
        }
        if (isset($params['is_gc'])) {
            if ($params['is_gc'] != -1) {
                $query = $query->where('is_gc', $params['is_gc']);
            }
        }
        if (isset($params['nep_type'])) {
            if ($params['nep_type'] != -1) {
                $query = $query->where('nep_type', $params['nep_type']);
            }
        }
        if (isset($params['status'])) {
            if ($params['status'] != -1) {
                $query = $query->where('status', $params['status']);
            }
        }
        if (isset($params['is_audit'])) {
            $query = $query->where('is_audit', 0);
        } else {
            if ($this->office === 1) {
                $query = $query->where('is_audit', '!=', 4);
            }
            if ($this->office === 2) {
                $query = $query->whereIn('is_audit', [1, 3]);
            }
        }
        $projects = $query->whereIn('user_id',$this->seeIds)->get()->toArray();

        return $projects;
    }
    //获取项目信息
    public function getAllProjects(Request $request)
    {
        $params = $request->input();
        $this->getSeeIds($params['userid']);
        $projects = $this->allProjects($params);
        $ProjectC = new ProjectController();
        $type = Dict::getOptionsArrByName('工程类项目分类');
        $is_gc = Dict::getOptionsArrByName('是否为国民经济计划');
        $status = Dict::getOptionsArrByName('项目状态');
        $money_from = Dict::getOptionsArrByName('资金来源');
        $build_type = Dict::getOptionsArrByName('建设性质');
        $nep_type = Dict::getOptionsArrByName('国民经济计划分类');
        foreach ($projects as $k => $row) {
            $projects[$k]['amount'] = number_format($row['amount'], 2);
            $projects[$k]['land_amount'] = isset($row['land_amount']) ? number_format($row['land_amount'], 2) : '';
            $projects[$k]['type'] = $type[$row['type']];
            $projects[$k]['is_gc'] = $is_gc[$row['is_gc']];
            $projects[$k]['status'] = $status[$row['status']];
            $projects[$k]['money_from'] = $money_from[$row['money_from']];
            $projects[$k]['build_type'] = $build_type[$row['build_type']];
            $projects[$k]['nep_type'] = isset($row['nep_type']) ? $nep_type[$row['nep_type']] : '';
            $projects[$k]['projectPlan'] = $ProjectC->getPlanData($row['id'], 'preview');
            $projects[$k]['scheduleInfo'] = ProjectSchedule::where('project_id', $row['id'])->orderBy('id', 'desc')->first();
            $projects[$k]['unit'] = Departments::where('id', $row['unit'])->value('title');
        }

        return response()->json(['result' => $projects], 200);
    }
    /**
     * 获取项目进度列表
     *
     * @return JsonResponse
     */
    public function projectProgressM($data)
    {
        $query = new ProjectSchedule;
        if (isset($data['department_id'])) {
            if (gettype($data['department_id']) == 'string') {
                $data['department_id'] = explode(',', $data['department_id']);
            }
            if (count($data['department_id']) > 0) {
                $user_ids = DB::table('users')->select('id')->where('department_id', $data['department_id'][1])->get()->toArray();
                $user_id = array_column($user_ids, 'id');
                $query = $query->whereIn('user_id', $user_id);
            }
        }
        if (isset($data['title']) || isset($data['project_num']) || isset($data['subject']) || isset($data['money_from']) || isset($data['is_gc']) || isset($data['nep_type'])) {
            $projects = Projects::select('id');
            if (isset($data['title'])) {
                $projects = $projects->where('title', 'like', '%' . $data['title'] . '%');
            }

            if (isset($data['project_num'])) {
                $projects = $projects->where('num', $data['project_num']);
            }
            if (isset($data['subject'])) {
                $projects = $projects->where('subject', 'like', '%' . $data['subject'] . '%');
            }
            if (isset($data['money_from'])) {
                if ($data['money_from'] != -1) {
                    $projects = $projects->where('money_from', $data['money_from']);
                }
            }
            if (isset($data['is_gc'])) {
                if ($data['is_gc'] != -1) {
                    $projects = $projects->where('is_gc', $data['is_gc']);
                }
            }
            if (isset($data['nep_type'])) {
                if ($data['nep_type'] != -1) {
                    $projects = $projects->where('nep_type', $data['nep_type']);
                }
            }
            $projects = $projects->get()->toArray();
            $ids = array_column($projects, 'id');
            $query = $query->whereIn('project_id', $ids);
        }
        if (isset($data['end_at'])) {
            $data['end_at'] = date('Y-m', strtotime($data['end_at']));
            $query = $query->where('month', $data['end_at']);
        }
        if (isset($data['is_audit'])) {
            $query = $query->where('is_audit', 0);
        }
        if ($this->office === 1) {
            $query = $query->where('is_audit', '!=', 4);
        }
        if ($this->office === 2) {
            $query = $query->where('is_audit', 1);
        }
        $ProjectSchedules = $query->whereIn('user_id', $this->seeIds);
        return $ProjectSchedules;
    }
    public function projectProgressList(Request $request)
    {
        $params = $request->all();
        $this->getSeeIds($params['userid']);
        $result = $this->projectProgressM($params);
        $ProjectSchedules = $result->orderBy('is_audit', 'desc')->get()->toArray();
        foreach ($ProjectSchedules as $k => $row) {
            $Projects = Projects::where('id', $row['project_id'])->first();
            $ProjectSchedules[$k]['money_from'] = $Projects['money_from'];
            $ProjectSchedules[$k]['project_title'] = $Projects['title'];
            $ProjectSchedules[$k]['acc_complete'] = $this->allActCompleteMoney($row['project_id'], $row['month']);
            $users = user::where('id', $row['user_id'])->first();
            $ProjectSchedules[$k]['tianbao_name'] = $users['name'];
            $ProjectSchedules[$k]['department'] = Departments::where('id', $users['department_id'])->value('title');
            $year = date('Y', strtotime($row['month']));
            $ProjectPlans = ProjectPlan::where('project_id', $row['project_id'])->where('date', $year)->first();
            $ProjectSchedules[$k]['project_num'] = $Projects['num'];
            $ProjectSchedules[$k]['subject'] = $Projects['subject'];
            $ProjectSchedules[$k]['build_start_at'] = $Projects['plan_start_at'];
            $ProjectSchedules[$k]['build_end_at'] = $Projects['plan_end_at'];
            $ProjectSchedules[$k]['total_investors'] = $Projects['amount'];
            $ProjectSchedules[$k]['plan_investors'] = $ProjectPlans['amount'];
            $ProjectSchedules[$k]['plan_img_progress'] = $ProjectPlans['img_progress'];
            $ProjectSchedules[$k]['plan_build_start_at'] = $Projects['plan_start_at'];
        }
        return response()->json(['result' => $ProjectSchedules], 200);
    }
    //获取进度详细信息
    public function projectScheduleInfo(Request $request)
    {
        $params = $request->all();
        $this->getSeeIds($params['userid']);
        $ProjectSchedules = ProjectSchedule::where('id', $params['id'])->first();
        
        $Projects = Projects::where('id', $ProjectSchedules['project_id'])->first();
        $ProjectSchedules['money_from'] = $Projects['money_from'];
        $ProjectSchedules['project_title'] = $Projects['title'];
        $ProjectSchedules['acc_complete'] = $this->allActCompleteMoney($ProjectSchedules['project_id'], $ProjectSchedules['month']);
        $users = user::where('id', $ProjectSchedules['user_id'])->first();
        $ProjectSchedules['tianbao_name'] = $users['name'];
        $ProjectSchedules['department'] = Departments::where('id', $users['department_id'])->value('title');
        $year = date('Y', strtotime($ProjectSchedules['month']));
        $ProjectPlans = ProjectPlan::where('project_id', $ProjectSchedules['project_id'])->where('date', $year)->first();
        $ProjectSchedules['project_num'] = $Projects['num'];
        $ProjectSchedules['subject'] = $Projects['subject'];
        $ProjectSchedules['build_start_at'] = $Projects['plan_start_at'];
        $ProjectSchedules['build_end_at'] = $Projects['plan_end_at'];
        $ProjectSchedules['total_investors'] = $Projects['amount'];
        $ProjectSchedules['plan_investors'] = $ProjectPlans['amount'];
        $ProjectSchedules['plan_img_progress'] = $ProjectPlans['img_progress'];
        $ProjectSchedules['plan_build_start_at'] = $Projects['plan_start_at'];
        return response()->json(['result' => $ProjectSchedules], 200);
    }
    //获取预警列表
    public function getAllWarning(Request $request)
    {
        $params = $request->input();
        $this->getSeeIds($params['userid']);
        $data = [];
        $projects = Projects::whereIn('user_id', $this->seeIds)->get()->toArray();
        $projectIds = array_column($projects, 'id');
        $projectSchedules = ProjectSchedule::whereIn('project_id', $projectIds)->get()->toArray();
        $scheduleIds = array_column($projectSchedules, 'id');

        $earlyWarning = new ProjectEarlyWarning;
        if (isset($params['warning_type'])) {
            if ($params['warning_type'] != -1) {
                $earlyWarning = $earlyWarning->where('warning_type', $params['warning_type']);
            }
        }
        if (isset($params['start_at']) && isset($params['end_at'])) {
            $params['start_at'] = date('Y-m', strtotime($params['start_at']));
            $params['end_at'] = date('Y-m', strtotime($params['end_at']));
            $earlyWarning = $earlyWarning->whereBetween('schedule_at', [$params['start_at'], $params['end_at']]);
        }
        $result = $earlyWarning->whereIn('schedule_id', $scheduleIds)->get()->toArray();
        foreach ($result as $k => $row) {
            $data[$k]['key'] = $row['id'];
            $res = ProjectSchedule::where('id', $row['schedule_id'])->first();
            foreach ($projects as $kk => $v) {
                if ($v['id'] === (int) $res->project_id) {
                    $data[$k]['title'] = $v['title'];
                }
            }
            $data[$k]['project_id'] = $res->project_id;
            $data[$k]['problem'] = $res->problem;
            $data[$k]['tags'] = $row['warning_type'];
            $data[$k]['schedule_at'] = $row['schedule_at'];
        }

        return response()->json(['result' => $data], 200);
    }
    //项目详细信息
    public function projectInfo(Request $request)
    {
        $params = $request->all();
        $this->getSeeIds($params['userid']);
        $projectInfo = Projects::where('id', $params['id'])->first();
        $type = Dict::getOptionsArrByName('工程类项目分类');
        $is_gc = Dict::getOptionsArrByName('是否为国民经济计划');
        $status = Dict::getOptionsArrByName('项目状态');
        $money_from = Dict::getOptionsArrByName('资金来源');
        $build_type = Dict::getOptionsArrByName('建设性质');
        $nep_type = Dict::getOptionsArrByName('国民经济计划分类');
        
        $projectInfo['amount'] = number_format($projectInfo['amount'], 2);
        $projectInfo['land_amount'] = isset($projectInfo['land_amount']) ? number_format($projectInfo['land_amount'], 2) : '';
        $projectInfo['type'] = $type[$projectInfo['type']];
        $projectInfo['is_gc'] = $is_gc[$projectInfo['is_gc']];
        $projectInfo['status'] = $status[$projectInfo['status']];
        $projectInfo['money_from'] = $money_from[$projectInfo['money_from']];
        $projectInfo['build_type'] = $build_type[$projectInfo['build_type']];
        $projectInfo['nep_type'] = isset($projectInfo['nep_type']) ? $nep_type[$projectInfo['nep_type']] : '';
        return response()->json(['result' => $projectInfo], 200);
    }
    /**
     * 查询项目计划
     *
     * @return JsonResponse
     */
    public function projectPlanInfo(Request $request)
    {
        $data = $request->input();
        $year = date('Y');
        if ($data['month']) {
            $year = date('Y', strtotime($data['month']));
        }
        $plans = DB::table('iba_project_plan')->where('date', $year)->where('project_id', $data['project_id'])->where('parent_id', 0)->first();

        return response()->json(['result' => $plans], 200);
    }
    /**
     * 累计投资
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function allActCompleteMoney($project_id, $month)
    {
        $plan_date = ProjectPlan::select('date', 'amount')->where('project_id', $project_id)->where('parent_id', 0)->get()->toArray();
        $result = 0;
        foreach ($plan_date as $k) {
            if ($k['date'] < 2019) {
                $result = $result + $k['amount'];
            }
        }
        $allMonth = ProjectSchedule::where('project_id', $project_id)->where('month', '<=', $month)->sum('month_act_complete');
        $result = $result + $allMonth;
        return $result;
    }
    /**
     * 填报，当当月实际投资发生改变时，修改累计投资
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function actCompleteMoney(Request $request)
    {
        $params = $request->input();
        $plan_date = ProjectPlan::select('date', 'amount')->where('project_id', $params['project_id'])->where('parent_id', 0)->get()->toArray();
        if ($params['month']) {
            $month = date('Y-m', strtotime($params['month']));
            $year = date('Y', strtotime($params['month']));
        }
        $result = 0;
        foreach ($plan_date as $k) {
            if ($k['date'] < 2019) {
                $result = $result + $k['amount'];
            } else {
                $sum = ProjectSchedule::where('project_id', $params['project_id'])->where('month', 'like', $k['date'] . '%')->where('user_id','!=','')->sum('month_act_complete');
                $result = $result + $sum;
            }
        }
        $result = $result + $params['month_act_complete'];
        if ($params['type'] == 'edit') {
            $month_money = ProjectSchedule::where('project_id', $params['project_id'])->where('month', $month)->value('month_act_complete');
            $result = $result - $month_money;
        }
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
        $project_title = Projects::where('id', $params['project_id'])->value('title');
        $suffix = $params['img_pic']->getClientOriginalExtension();
        $path = Storage::putFileAs(
            'public/project/project-schedule/' . $project_title . '/' . $params['month'],
            $request->file('img_pic'),
            rand(1000000, time()) . '.' . $suffix
        );
        $path = 'storage/' . substr($path, 7);
        return response()->json(['result' => $path], 200);
    }

    /**
     * 审核项目进度填报
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function auditProjectProgress(Request $request)
    {
        $data = $request->all();
        $result = ProjectSchedule::where('id', $data['id'])->update(['is_audit' => $data['status'], 'reason' => $data['reason']]);
        $projects = ProjectSchedule::where('id', $data['id'])->first();
        $year = (int) date('Y', strtotime($projects['month']));
        $month = (int) date('m', strtotime($projects['month']));
        $y = intval($year);
        $m = intval($month);
        // 年计划
        $plans_amount_y = DB::table('iba_project_plan')->where('project_id', $projects['project_id'])->where('parent_id', 0)->where('date', $y)->value('id');
        // 月计划
        $plans_amount = DB::table('iba_project_plan')->where('project_id', $projects['project_id'])->where('parent_id', $plans_amount_y)->where('date', $m)->value('amount');
        if ($data['status'] == 1) {
            $warResult = true;
            $warData = [];
            if ($plans_amount) {
                $Percentage = $projects['month_act_complete'] / $plans_amount;
                if ($Percentage < 1) {
                    if ($Percentage >= 0.7 && $Percentage < 1) {
                        $warData['warning_type'] = 1;
                    } elseif ($Percentage < 0.7) {
                        $warData['warning_type'] = 2;
                    }
                    $warData['schedule_id'] = $data['id'];
                    $warData['schedule_at'] = date('Y-m');
                    $warResult = ProjectEarlyWarning::insert($warData);
                }
            }
        }
        $result = $result || $result >= 0;

        return response()->json(['result' => $result], 200);
    }
    /**
     * 修改项目进度填报
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function editProjectProgress(Request $request)
    {
        $data = $request->all();
        $data['month'] = date('Y-m', strtotime($data['month']));
        $data['build_start_at'] = date('Y-m', strtotime($data['build_start_at']));
        $data['build_end_at'] = date('Y-m', strtotime($data['build_end_at']));
        if ($data['plan_build_start_at']) {
            $data['plan_build_start_at'] = date('Y-m', strtotime($data['plan_build_start_at']));
        }
        if ($this->office === 0) {
            if ($data['is_audit'] === 2 || $data['is_audit'] === 3) {
                $data['is_audit'] = 4;
            }
        }
        $id = $data['id'];
        $path = 'storage/project/project-schedule/' . $data['project_title'] . '/' . $data['month'];
        if ($data['img_progress_pic']) {
            $imgProgressPic = explode(',', $data['img_progress_pic']);
            $handler = opendir($path);
            while (($filename = readdir($handler)) !== false) {
                if ($filename != "." && $filename != "..") {
                    if (!in_array($path . '/' . $filename, $imgProgressPic)) {
                        unlink($path . '/' . $filename);
                    }
                }
            }
        } else {
            if (file_exists($path)) {
                $handler = opendir($path);
                while (($filename = readdir($handler)) !== false) {
                    if ($filename != "." && $filename != "..") {
                        unlink($path . '/' . $filename);
                    }
                }
            }
        }
        unset($data['id'], $data['updated_at'], $data['project_id'], $data['subject'], $data['project_num'],
            $data['build_start_at'], $data['build_end_at'], $data['total_investors'], $data['plan_start_at'],
            $data['plan_investors'], $data['plan_img_progress'], $data['month'], $data['project_title']);
        $result = ProjectSchedule::where('id', $id)->update($data);

        //        if ($result) {
        //            $log = new OperationLog();
        //            $log->eventLog($request, '修改项目进度信息');
        //        }

        return response()->json(['result' => $result], 200);
    }
}
                     