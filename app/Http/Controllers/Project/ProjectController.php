<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\Projects;
use App\Models\Project\ProjectPlan;
use App\Models\Project\ProjectSchedule;
use App\Models\Project\ProjectLedger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\OperationLog;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectEarlyWarning;
use Illuminate\Support\Facades\Storage;
use App\Models\Dict;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * 获取一级项目信息
     *
     * @return JsonResponse
     */
    public function getProjects()
    {
        $projects = Projects::all()->toArray();

        $projectIds = ProjectPlan::all()->pluck('project_id')->toArray();
        $projectIds = array_unique($projectIds);

        foreach ($projects as $key => $value) {
            $projects[$key]['parent_title'] = '一级项目';
            $projects[$key]['deep'] = 1;
            $projects[$key]['is_parent'] = in_array($value['id'], $projectIds, true);
        }

        return response()->json(['result' => $projects], 200);
    }

    public function loadPlan($project_id)
    {
        $yearPlans = ProjectPlan::where('project_id', $project_id)->where('parent_id', 0)->get()->toArray();

        $data = [];
        foreach ($yearPlans as $key => $value) {
            $value['children'] = $this->getChildPlan($value['id']);
            $value['key'] = $value['id'];
            $value['deep'] = 2;
            $data[] = $value;
        }

        return response()->json(['result' => $data], 200);
    }

    public function getChildPlan($parent_id)
    {
        $data = [];
        $monthPlans = ProjectPlan::where('parent_id', $parent_id)->get()->toArray();
        foreach ($monthPlans as $key => $value) {
            $value['key'] = $value['id'];
            $value['deep'] = 3;
            $data[] = $value;
        }

        return $data;
    }

    /**
     * 创建项目信息
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $data = $request->input();
        $data['plan_start_at'] = date('Y-m', strtotime($data['plan_start_at']));
        $data['plan_end_at'] = date('Y-m', strtotime($data['plan_end_at']));
        $data['positions'] = self::buildPositions($data['positions']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['is_audit'] = 0;
        $data['is_edit'] = 0;
        $data['user_id'] = Auth::id();

        $planData = $data['projectPlan'];

        unset($data['projectPlan']);

        $id = DB::table('iba_project_projects')->insertGetId($data);
        $this->insertPlan($id, $planData);

        $result = $id ? true : false;

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '创建项目信息');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 添加项目计划
     *
     * @param $projectId
     * @param $planData
     */
    public function insertPlan($projectId, $planData)
    {
        foreach ($planData as $k => $v) {
            $v['project_id'] = $projectId;
            $v['parent_id'] = 0;
            $v['created_at'] = date('Y-m-d H:i:s');
            $monthArr = $v['month'];
            unset($v['month']);

            $parentId = DB::table('iba_project_plan')->insertGetId($v);

            foreach ($monthArr as $k => $month) {
                $month['project_id'] = $projectId;
                $month['parent_id'] = $parentId;
                $month['created_at'] = date('Y-m-d H:i:s');

                ProjectPlan::insert($month);
            }
        }
    }

    /**
     * 获取一段时间内的所有月份
     *
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function getMonthList($startDate, $endDate)
    {
        $yearStart = date('Y', $startDate);
        $monthStart = date('m', $startDate);

        $yearEnd = date('Y', $endDate);
        $monthEnd = date('m', $endDate);

        if ($yearStart == $yearEnd) {
            $monthInterval = $monthEnd - $monthStart;
        } elseif ($yearStart < $yearEnd) {
            $yearInterval = $yearEnd - $yearStart - 1;
            $monthInterval = (12 - $monthStart + $monthEnd) + 12 * $yearInterval;
        }
        //循环输出月份
        $data = [];
        for ($i = 0; $i <= $monthInterval; $i++) {
            $tmpTime = mktime(0, 0, 0, $monthStart + $i, 1, $yearStart);
            $data[$i]['year'] = date('Y', $tmpTime);
            $data[$i]['month'] = date('m', $tmpTime);
        }
        unset($tmpTime);

        $data = collect($data)->groupBy('year')->toArray();

        return $data;
    }

    public function addProjectPlan(Request $request)
    {
        $data = $request->input();
        $result = ProjectPlan::insert($data);

        // if ($result) {
        //     $log = new OperationLog();
        //     $log->eventLog($request, '创建项目计划');
        // }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 修改项目信息
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->input();
        $id = $data['id'];
        if ($data['deep'] === 1) {
            $data['plan_start_at'] = date('Y-m', strtotime($data['plan_start_at']));
            $data['plan_end_at'] = date('Y-m', strtotime($data['plan_end_at']));
            if ($data['is_parent']) {
                unset($data['loading'], $data['children']);
            }
            unset($data['id'], $data['updated_at'], $data['expand'], $data['parent_title'], $data['deep'], $data['is_parent'], $data['nodeKey'], $data['selected']);

            $result = Projects::where('id', $id)->update($data);
        } else {
            unset($data['id'], $data['updated_at'], $data['children'], $data['key'], $data['deep'], $data['nodeKey'], $data['selected']);
            if ($data['expand']) {
                unset($data['expand']);
            }
            $result = ProjectPlan::where('id', $id)->update($data);
        }

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '修改项目信息');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 获取所有项目信息
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllProjects(Request $request)
    {
        $params = $request->input('searchForm');
        $query = new Projects;
        if ($params['num']) {
            $query = $query::where('num', $params['num']);
        }
        $projects = $query->where('user_id', Auth::id())->get()->toArray();
        foreach ($projects as $k => $row) {
            $projects[$k]['type'] = Dict::getOptionsArrByName('工程类项目分类')[$row['type']];
            $projects[$k]['is_gc'] = Dict::getOptionsArrByName('是否为国民经济计划')[$row['is_gc']];
            $projects[$k]['status'] = Dict::getOptionsArrByName('项目状态')[$row['status']];
            $projects[$k]['money_from'] = Dict::getOptionsArrByName('资金来源')[$row['money_from']];
            $projects[$k]['build_type'] = Dict::getOptionsArrByName('建设性质')[$row['build_type']];
        }

        return response()->json(['result' => $projects], 200);
    }

    /**
     * 删除项目
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        $allIds = $request->all();
        if ($allIds['parentsIds']) {
            $parentsIds = explode(',', $allIds['parentsIds']);
            Projects::destroy($parentsIds);
            DB::table('iba_project_plan')->whereIn('project_id', $parentsIds)->delete();
        }
        if ($allIds['yearIds']) {
            $yearIds = explode(',', $allIds['yearIds']);
            ProjectPlan::destroy($yearIds);
            DB::table('iba_project_plan')->whereIn('parent_id', $yearIds)->delete();
        }
        if ($allIds['monthIds']) {
            $monthIds = explode(',', $allIds['monthIds']);
            ProjectPlan::destroy($monthIds);
        }

        return response()->json(['result' => true], 200);
    }

    public function getAllWarning()
    {
        $data = [];
        $result = ProjectEarlyWarning::all()->toArray();
        foreach ($result as $k => $row) {
            $data[$k]['key'] = $row['id'];
            $data[$k]['project_id'] = $row['project_id'];
            $data[$k]['title'] = $row['title'];
            $data[$k]['tags'] = $row['warning_type'];
        }
        return response()->json(['result' => $data], 200);
    }

    /**
     * 构建坐标集
     *
     * @param array $positions
     * @return string
     */
    public static function buildPositions($positions)
    {
        $result = [];
        if ($positions) {
            foreach ($positions as $key => $value) {
                if ($value['status'] === 1) {
                    $result[] = $value['value'];
                }
            }
        }

        return implode(';', $result);
    }

    /**
     * 项目信息填报
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function projectProgress(Request $request)
    {
        $data = $request->input();
        if ($data['month']) {
            $data['month'] = date('Y-m', strtotime($data['month']));
            $year = date('Y', strtotime($data['month']));
            $month = date('m', strtotime($data['month']));
        }
        if ($data['build_start_at']) {
            $data['build_start_at'] = date('Y-m', strtotime($data['build_start_at']));
        }
        if ($data['build_end_at']) {
            $data['build_end_at'] = date('Y-m', strtotime($data['build_end_at']));
        }
        if ($data['start_at']) {
            $data['start_at'] = date('Y-m', strtotime($data['start_at']));
        }
        if ($data['plan_start_at']) {
            $data['plan_start_at'] = date('Y-m', strtotime($data['plan_start_at']));
        }
        if ($data['img_progress_pic']) {
            $data['img_progress_pic'] = substr($data['img_progress_pic'], 1);
        }
        $data['is_audit'] = 0;
        $result = ProjectSchedule::insert($data);
        
        
        $plan_id = DB::table('iba_project_plan')->where('project_id',$data['project_id'])->where('date', $year)->value('id');
        $plans_amount = DB::table('iba_project_plan')->where('project_id',$data['project_id'])->where('parent_id',$plan_id)->where('date', $month)->value('amount');
        $Percentage=($plans_amount-$data['month_act_complete'])/$plans_amount;
        if($Percentage<=0.1){
            $warData['warning_type'] = 0;
        }else if($Percentage>0.1&&$Percentage<=0.2){
            $warData['warning_type'] = 1;
        }else if($Percentage>0.2){
            $warData['warning_type'] = 2;
        }
        $warData['project_id'] = $data['project_id'];
        $warData['title'] = $year.'年'.$month.'月项目预警信息';
        $warResult =ProjectEarlyWarning::insert($warData);
        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '投资项目进度填报');
        }
        
        return response()->json(['result' => $result], 200);
    }

    /**
     * 获取项目进度列表
     *
     * @return JsonResponse
     */
    public function projectProgressList(Request $request)
    {
        $data = $request->input();
        $ProjectSchedules = ProjectSchedule::all()->toArray();
        if ($data['search_project_id']) {
            $ProjectSchedules = ProjectSchedule::where('project_id', $data['search_project_id'])->get()->toArray();
        }
        foreach ($ProjectSchedules as $k => $row) {
            $Projects = Projects::where('id', $row['project_id'])->value('title');
            $ProjectSchedules[$k]['project_id'] = $Projects;
        }
        return response()->json(['result' => $ProjectSchedules], 200);
    }

    /**
     * 上传
     *
     * @return JsonResponse
     */
    public function uploadPic(Request $request)
    {
        $path = Storage::putFile(
            '/public/project/project-schedule', $request->file('img_pic')
        );

        return response()->json(['result' => $path], 200);
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
        $plans = DB::table('iba_project_plan')->where('date', $year)->first();

        return response()->json(['result' => $plans], 200);
    }

    /**
     * 查询数据字典
     *
     * @return JsonResponse
     */
    public function getData(Request $request)
    {
        $params = $request->input();
        $data = Dict::getOptionsByName($params['title']);

        return response()->json(['result' => $data], 200);
    }

    /**
     * 获取项目库表单中的数据字典数据
     *
     * @param Request $request
     * @return array
     */
    public function getProjectDictData(Request $request)
    {
        $nameArr = $request->input('dictName');
        $result = Projects::getDictDataByName($nameArr);

        return response()->json(['result' => $result], 200);
    }

    /**
     * 获取台账进度列表
     *
     * @return JsonResponse
     */
    public function projectLedgerList(Request $request)
    {
        $data = $request->input();
        $ProjectLedger = ProjectLedger::where('id','>',0);
        if ($data['search_project_id']) {
            $ProjectLedger = $ProjectLedger->where('project_id', $data['search_project_id']);
        }
        if ($data['search_year']) {
            $ProjectLedger = $ProjectLedger->where('year', date('Y', strtotime($data['search_year'])));
        }
        $ProjectLedger=$ProjectLedger->get()->toArray();
        foreach ($ProjectLedger as $k => $row) {
            $Projects = Projects::where('id', $row['project_id'])->value('title');
            $ProjectLedger[$k]['project_id'] = $Projects;
            $nature = Dict::getOptionsByName('建设性质');
            $ProjectLedger[$k]['nature'] = $nature[$row['nature']]['title'];
            $quarter = Dict::getOptionsByName('季度');
            $ProjectLedger[$k]['quarter'] = $quarter[$row['quarter']]['title'];
        }
        return response()->json(['result' => $ProjectLedger], 200);
    }

    /**
     * 项目季度改变项目名称，填写其他字段
     *
     * @return JsonResponse
     */
    public function projectQuarter(Request $request)
    {
        $params = $request->input();
        if ($params['dictName']['year']) {
            $year = date('Y', strtotime($params['dictName']['year']));
        }
        $quarter = $params['dictName']['quarter'];
        if ($quarter == 0) {
            $date = $year . '-03';
        } elseif ($quarter == 1) {
            $date = $year . '-06';
        } elseif ($quarter == 2) {
            $date = $year . '-09';
        } elseif ($quarter == 3) {
            $date = $year . '-12';
        }
        $project_id = $params['dictName']['project_id'];
        $result = [];
        $result['projects'] = Projects::where('id', $project_id)->first();

        $result['ProjectSchedules'] = ProjectSchedule::where('project_id', $project_id)->where('month', $date)->first();
        // $ProjectLedger = ProjectLedger::all()->toArray();

        return response()->json(['result' => $result], 200);
    }

    /**
     * 添加台账
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function projectLedgerAdd(Request $request)
    {
        $data = $request->input();
        $params = $data['dictName'];
        if ($params['year']) {
            $params['year'] = date('Y', strtotime($params['year']));
        }
        $result = ProjectLedger::insert($params);

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '添加台账');
        }

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
        $data = $request->input();
        $data = $data['dictName'];
        $id = $data['id'];
        if ($data['start_at']) {
            $data['start_at'] = date('Y-m', strtotime($data['start_at']));
        }
        if ($data['img_progress_pic']) {
            $data['img_progress_pic'] = substr($data['img_progress_pic'], 1);
        }
        unset($data['id'], $data['updated_at'], $data['project_id'], $data['subject'], $data['project_num'], $data['build_start_at'], $data['build_end_at'], $data['total_investors'], $data['plan_start_at'], $data['plan_investors'], $data['plan_img_progress'], $data['month']);

        $result = ProjectSchedule::where('id', $id)->update($data);

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '修改项目进度信息');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 审核项目进度填报
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function auditProjectProgress(Request $request)
    {
        $data = $request->input();
        $id = $data['id'];
        $result = ProjectSchedule::where('id', $id)->update(['is_audit' => $data['is_audit']]);

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '修改项目进度信息');
        }

        return response()->json(['result' => $result], 200);
    }

    public function buildPlanFields(Request $request)
    {
        $date = $request->input('date');

        $start = strtotime($date[0]);
        $end = strtotime($date[1]);
        $dateList = self::getMonthList($start, $end);

        $data = [];
        $i = 0;
        foreach ($dateList as $year => $month) {
            $data[$i] = [
                'date' => $year,
                'amount' => '',
                'image_progress' => ''
            ];
            $monthList = [];
            $ii = 0;
            foreach ($month as $k => $v) {
                $monthList[$ii] = [
                    'date' => (int)$v['month'],
                    'amount' => '',
                    'image_progress' => ''
                ];
                $ii++;
            }
            $data[$i]['month'] = $monthList;
            $i++;
        }

        return response()->json(['result' => $data], 200);
    }

    public function auditProject(Request $request)
    {
        $data = $request->input('status');
        $result = Projects::where('id', $data['id'])->update(['is_audit' => $data['status']]);

        $result = $result ? true : false;

        return response()->json(['result' => $result], 200);
    }

}
