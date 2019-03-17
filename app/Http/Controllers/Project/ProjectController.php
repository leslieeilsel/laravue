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
        $projects = Projects::select('id', 'title')->get()->toArray();

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
        $data['plan_start_at'] = date('Y-m', strtotime($data['plan_start_at']));
        $data['plan_end_at'] = date('Y-m', strtotime($data['plan_end_at']));
        $data['is_audit'] = 0;
        $id = $data['id'];
        $projectPlan = $data['projectPlan'];
        unset($data['id'], $data['projectPlan'], $data['positions'], $data['center_point']);
        $result = Projects::where('id', $id)->update($data);
        $deleteRes = ProjectPlan::where('project_id', $id)->delete();
        $this->insertPlan($id, $projectPlan);

        $result = ($result >= 0 && $deleteRes >= 0) ? true : false;

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
        if (isset($params['title'])) {
            $query = $query->where('title', $params['title']);
        }
        if (isset($params['subject'])) {
            $query = $query->where('subject', $params['subject']);
        }
        if (isset($params['unit'])) {
            $query = $query->where('unit', $params['unit']);
        }
        if (isset($params['num'])) {
            $query = $query->where('num', $params['num']);
        }
        if (isset($params['type'])) {
            $query = $query->where('type', $params['type']);
        }
        if (isset($params['build_type'])) {
            $query = $query->where('build_type', $params['build_type']);
        }
        if (isset($params['money_from'])) {
            $query = $query->where('money_from', $params['money_from']);
        }
        if (isset($params['is_gc'])) {
            $query = $query->where('is_gc', $params['is_gc']);
        }
        if (isset($params['nep_type'])) {
            $query = $query->where('nep_type', $params['nep_type']);
        }
        if (isset($params['status'])) {
            $query = $query->where('status', $params['status']);
        }
        $group_id = Auth::user()->group_id;
        if ($group_id === 4 || $group_id === 7) {
            $user_id = [1, 2, 3, 4, 5, 6, 7, 8];
        }
        if ($group_id === 6) {
            $user_id = [7];
        }
        $projects = $query->whereIn('user_id', $user_id)->get()->toArray();
        foreach ($projects as $k => $row) {
            $projects[$k]['amount'] = number_format($row['amount'], 2);
            $projects[$k]['land_amount'] = isset($row['land_amount']) ? number_format($row['land_amount'], 2) : '';
            $projects[$k]['type'] = Dict::getOptionsArrByName('工程类项目分类')[$row['type']];
            $projects[$k]['is_gc'] = Dict::getOptionsArrByName('是否为国民经济计划')[$row['is_gc']];
            $projects[$k]['status'] = Dict::getOptionsArrByName('项目状态')[$row['status']];
            $projects[$k]['money_from'] = Dict::getOptionsArrByName('资金来源')[$row['money_from']];
            $projects[$k]['build_type'] = Dict::getOptionsArrByName('建设性质')[$row['build_type']];
            $projects[$k]['nep_type'] = isset($row['nep_type']) ? Dict::getOptionsArrByName('国民经济计划分类')[$row['nep_type']] : '';
            $projects[$k]['projectPlan'] = $this->getPlanData($row['id'], 'preview');
        }

        return response()->json(['result' => $projects], 200);
    }

    public function getEditFormData(Request $request)
    {
        $id = $request->input('id');

        $projects = Projects::where('id', $id)->first()->toArray();

        $projects['plan_start_at'] = date('Y-m', strtotime($projects['plan_start_at']));
        $projects['plan_end_at'] = date('Y-m', strtotime($projects['plan_end_at']));

        $projects['projectPlan'] = $this->getPlanData($id, 'edit');

        return response()->json(['result' => $projects], 200);
    }

    /**
     * 获取计划数据
     *
     * @param integer $project_id
     * @param string $status
     * @return array
     */
    public function getPlanData($project_id, $status)
    {
        $projectPlans = ProjectPlan::where('project_id', $project_id)->where('parent_id', 0)->get()->toArray();
        $data = [];
        foreach ($projectPlans as $k => $row) {
            $data[$k]['date'] = $row['date'];
            $data[$k]['amount'] = $status === 'preview' ? number_format($row['amount'], 2) : $row['amount'];
            $data[$k]['image_progress'] = $row['image_progress'];
            $monthPlan = ProjectPlan::where('parent_id', $row['id'])->get()->toArray();
            foreach ($monthPlan as $key => $v) {
                $data[$k]['month'][$key]['date'] = $v['date'];
                $data[$k]['month'][$key]['amount'] = $status === 'preview' ? number_format($v['amount'], 2) : $v['amount'];
                $data[$k]['month'][$key]['image_progress'] = $v['image_progress'];
            }
        }

        return $data;
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
            $data[$k]['shedeule_at'] = $row['shedeule_at'];
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
        if ($data['plan_build_start_at']) {
            $data['plan_build_start_at'] = date('Y-m', strtotime($data['plan_build_start_at']));
        }
        if ($data['img_progress_pic']) {
            $data['img_progress_pic'] = substr($data['img_progress_pic'], 1);
        }
        $data['is_audit'] = 0;
        $data['created_at'] = date('Y-m-d H:i:s');
        $result = ProjectSchedule::insert($data);

        $plan_id = DB::table('iba_project_plan')->where('project_id', $data['project_id'])->where('date', $year)->value('id');
        $plans_amount = DB::table('iba_project_plan')->where('project_id', $data['project_id'])->where('parent_id', $plan_id)->where('date', $month)->value('amount');
        $Percentage = ($plans_amount - $data['month_act_complete']) / $plans_amount;
        if ($Percentage <= 0.1) {
            $warData['warning_type'] = 0;
        } elseif ($Percentage > 0.1 && $Percentage <= 0.2) {
            $warData['warning_type'] = 1;
        } elseif ($Percentage > 0.2) {
            $warData['warning_type'] = 2;
        }
        $warData['project_id'] = $data['project_id'];
        $warData['shedeule_at'] = $year . '-' . $month;
        $warData['title'] = Projects::where('id', $data['project_id'])->value('title');
        $warResult = ProjectEarlyWarning::insert($warData);
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
        $data = $request->all();
        $query = new ProjectSchedule;
        if (isset($data['project_id'])) {
            $query = $query->where('project_id', $data['search_project_id']);
        }
        if (isset($data['project_num'])) {
            $query = $query->where('project_num', $data['project_num']);
        }
        if (isset($data['subject'])) {
            $query = $query->where('subject', $data['subject']);
        }
        if (isset($data['start_at']) && isset($data['end_at'])) {
            $data['start_at'] = date('Y-m', strtotime($data['start_at']));
            $data['end_at'] = date('Y-m', strtotime($data['end_at']));
            $query = $query->whereBetween('month', [$data['start_at'], $data['end_at']]);
        }
        $group_id = Auth::user()->group_id;
        if ($group_id === 4 || $group_id === 7) {
            $user_id = [1, 2, 3, 4, 5, 6, 7, 8];
        }
        if ($group_id === 6) {
            $user_id = [7];
        }
        $ProjectSchedules = $query->whereIn('user_id', $user_id)->get()->toArray();
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
            'public/project/project-schedule',
            $request->file('img_pic')
        );

        $path = 'storage/' . substr($path, 7);

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
        $params = $request->input();
        $sql = ProjectSchedule::where('id', '>', 0);
        $year = date('Y', strtotime($params['search_year']));
        if ($params['search_year']) {
            if ($quarter = $params['search_quarter']) {
                if ($quarter == 0) {
                    $sql = $sql->whereIn('month', [$year . '-01', $year . '-02', $year . '-03']);
                } elseif ($quarter == 1) {
                    $sql = $sql->whereIn('month', [$year . '-04', $year . '-05', $year . '-06']);
                } elseif ($quarter == 2) {
                    $sql = $sql->whereIn('month', [$year . '-07', $year . '-08', $year . '-09']);
                } elseif ($quarter == 3) {
                    $sql = $sql->whereIn('month', [$year . '-10', $year . '-11', $year . '-12']);
                }
            } else {
                $sql = $sql->where('month', 'like', $year . "%");
            }
        }
        if ($params['search_project_id']) {
            $sql = $sql->where('project_id', $params['search_project_id']);
        }
        $sql = $sql->get()->toArray();

        foreach ($sql as $k => $row) {
            $projects = Projects::where('id', $row['project_id'])->first();
            $sql[$k]['project_id'] = $projects['title'];
            $sql[$k]['project_num'] = $projects['num'];
            $sql[$k]['nature'] = Dict::getOptionsArrByName('建设性质')[$projects['build_type']];
            $sql[$k]['subject'] = $projects['subject'];
            $sql[$k]['total_investors'] = $projects['amount'];
            $sql[$k]['scale_con'] = $projects['description'];
        }

        return response()->json(['result' => $sql], 200);
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
        $result = ProjectSchedule::where('id', $id)->update(['is_audit' => $data['status']]);

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
                'image_progress' => '',
            ];
            $monthList = [];
            $ii = 0;
            foreach ($month as $k => $v) {
                $monthList[$ii] = [
                    'date' => (int)$v['month'],
                    'amount' => '',
                    'image_progress' => '',
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

    /**
     * 项目调整 ，改变审核状态
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function projectAdjustment(Request $request)
    {
        $result = Projects::where('id', '>', 0)->update(['is_audit' => 3]);

        $result = $result ? true : false;

        return response()->json(['result' => $result], 200);
    }
}
