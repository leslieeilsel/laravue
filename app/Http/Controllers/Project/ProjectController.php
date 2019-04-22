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
use App\Models\OperationLog;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectEarlyWarning;
use Illuminate\Support\Facades\Storage;
use App\Models\Dict;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;

class ProjectController extends Controller
{
    public $seeIds;
    public $office;

    public function __construct()
    {
        $this->getSeeIds();
    }

    public function getSeeIds()
    {
        if (Auth::check()) {
            $roleId = Auth::user()->group_id;
            $this->office = Auth::user()->office;
            $userId = Auth::id();
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

    /**
     * 获取一级项目信息
     *
     * @return JsonResponse
     */
    public function getProjects()
    {
        $query = new Projects();

        if ($this->office === 1) {
            $query = $query->where('is_audit', '!=', 4);
        }
        if ($this->office === 2) {
            $query = $query->where('is_audit', 1);
        }

        $projects = $query->whereIn('user_id', $this->seeIds)->get()->toArray();

        return response()->json(['result' => $projects], 200);
    }

    public function getAuditedProjects()
    {
        $projects = Projects::where('is_audit', 1)->whereIn('user_id', $this->seeIds)->get()->toArray();

        return response()->json(['result' => $projects], 200);
    }

    /**
     * 创建项目信息
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        if (Auth::check()) {
            $data = $request->input();
            $data['plan_start_at'] = date('Y-m', strtotime($data['plan_start_at']));
            $data['plan_end_at'] = date('Y-m', strtotime($data['plan_end_at']));
            $data['center_point'] = json_encode($data['center_point']);
            $data['positions'] = json_encode($data['positions']);
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['is_audit'] = 4;
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
        } else {
            return response()->json(['result' => false, 'message' => '登录超时，请重新登陆']);
        }
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
            if (isset($v['role'])) {
                unset($v['role']);
            }
            if (isset($v['placeholder'])) {
                unset($v['placeholder']);
            }

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
        $data['center_point'] = json_encode($data['center_point']);
        $data['positions'] = json_encode($data['positions']);
        if ($this->office === 0) {
            if ($data['is_audit'] === 2 || $data['is_audit'] === 3) {
                $data['is_audit'] = 4;
            }
        }
        $id = $data['id'];
        $data['reason'] = '';
        $projectPlan = $data['projectPlan'];
        unset($data['id'], $data['projectPlan']);
        $result = Projects::where('id', $id)->update($data);
        $this->updatePlan($id, $projectPlan);

        $result = ($result >= 0) ? true : false;

        return response()->json(['result' => $result], 200);
    }

    public function updatePlan($projectId, $planData)
    {
        foreach ($planData as $k => $v) {
            $v['project_id'] = $projectId;
            $planYearId = ProjectPlan::where('project_id', $projectId)->where('date', $v['date'])->first()->id;

            $v['parent_id'] = 0;
            $v['updated_at'] = date('Y-m-d H:i:s');
            $monthArr = $v['month'];
            unset($v['month']);
            $yearRes = ProjectPlan::where('id', $planYearId)->update($v);
            foreach ($monthArr as $k => $month) {
                $planMonthId = ProjectPlan::where('project_id', $projectId)
                    ->where('date', $month['date'])
                    ->where('parent_id', $planYearId)
                    ->first()
                    ->id;
                $month['updated_at'] = date('Y-m-d H:i:s');
                unset($month['date']);
                $monthRes = ProjectPlan::where('id', $planMonthId)->update($month);
            }
        }
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
                $query = $query->where('is_audit', 1);
            }
        }
        $projects = $query->whereIn('user_id', $this->seeIds)->get()->toArray();

        return $projects;
    }

    public function getAllProjects(Request $request)
    {
        $params = $request->input('searchForm');
        $projects = $this->allProjects($params);
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
            $projects[$k]['scheduleInfo'] = ProjectSchedule::where('project_id', $row['id'])->orderBy('id', 'desc')->first();
        }

        return response()->json(['result' => $projects], 200);
    }

    public function getEditFormData(Request $request)
    {
        $id = $request->input('id');

        $projects = Projects::where('id', $id)->first()->toArray();

        $projects['plan_start_at'] = date('Y-m', strtotime($projects['plan_start_at']));
        $projects['plan_end_at'] = date('Y-m', strtotime($projects['plan_end_at']));
        $projects['amount'] = (float)$projects['amount'];
        $projects['land_amount'] = $projects['land_amount'] ? (float)$projects['land_amount'] : null;

        $projects['projectPlan'] = $this->getPlanData($id, 'edit');

        return response()->json(['result' => $projects], 200);
    }

    /**
     * 获取计划数据
     *
     * @param integer $project_id
     * @param string  $status
     * @return array
     */
    public function getPlanData($project_id, $status)
    {
        $projectPlans = ProjectPlan::where('project_id', $project_id)->where('parent_id', 0)->get()->toArray();
        $data = [];
        foreach ($projectPlans as $k => $row) {
            $data[$k]['date'] = $row['date'];
            if ($status === 'preview') {
                $data[$k]['amount'] = isset($row['amount']) ? number_format($row['amount'], 2) : null;
            } else {
                $data[$k]['amount'] = isset($row['amount']) ? (float)$row['amount'] : null;
            }
            $data[$k]['image_progress'] = $row['image_progress'];
            $monthPlan = ProjectPlan::where('parent_id', $row['id'])->get()->toArray();
            foreach ($monthPlan as $key => $v) {
                $data[$k]['month'][$key]['date'] = $v['date'];
                if ($status === 'preview') {
                    $data[$k]['month'][$key]['amount'] = isset($v['amount']) ? number_format($v['amount'], 2) : null;
                } else {
                    $data[$k]['month'][$key]['amount'] = isset($v['amount']) ? (float)$v['amount'] : null;
                }
                $data[$k]['month'][$key]['image_progress'] = $v['image_progress'];
            }
        }

        return $data;
    }

    public function getAllWarning(Request $request)
    {
        $params = $request->input('searchForm');
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
                if ($v['id'] === (int)$res->project_id) {
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
        $data = $request->all();
        $data['month'] = date('Y-m', strtotime($data['month']));
        $year = (int)date('Y', strtotime($data['month']));
        $month = (int)date('m', strtotime($data['month']));
        $plan_id = DB::table('iba_project_plan')->where('project_id', $data['project_id'])->where('date', $year)->value('id');
        $plan_month_id = DB::table('iba_project_plan')->where('project_id', $data['project_id'])->where('parent_id', $plan_id)->where('date', $month)->value('id');

        $data['build_start_at'] = date('Y-m', strtotime($data['build_start_at']));
        $data['build_end_at'] = date('Y-m', strtotime($data['build_end_at']));
        if ($data['plan_build_start_at']) {
            $data['plan_build_start_at'] = date('Y-m', strtotime($data['plan_build_start_at']));
        }
        if ($data['img_progress_pic']) {
            $data['img_progress_pic'] = substr($data['img_progress_pic'], 1);
        }
        $data['is_audit'] = 4;
        $data['plan_id'] = $plan_month_id;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['user_id'] = Auth::id();
        $schedule_id = DB::table('iba_project_schedule')->insertGetId($data);
        $result = $schedule_id;
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
    public function projectProgressM($data)
    {
        $query = new ProjectSchedule;
        if (isset($data['department_id'])) {
            $user_ids = DB::table('users')->select('id')->where('department_id', $data['department_id'])->get()->toArray();
            $user_id = array_column($user_ids, 'id');
            $query = $query->whereIn('user_id', $user_id);
        }
        if (isset($data['title']) || isset($data['money_from']) || isset($data['is_gc']) || isset($data['nep_type'])) {
            $projects = Projects::select('id');
            if (isset($data['title'])) {
                $projects = $projects->where('title', 'like', '%' . $data['title'] . '%');
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
            $ids = array_intersect($ids, $this->seeIds);
            $query = $query->whereIn('project_id', $ids);
        }
        if (isset($data['project_num'])) {
            $query = $query->where('project_num', $data['project_num']);
        }
        if (isset($data['subject'])) {
            $query = $query->where('subject', 'like', '%' . $data['subject'] . '%');
        }
        if (isset($data['start_at']) || isset($data['end_at'])) {
            if (isset($data['start_at']) && isset($data['end_at'])) {
                $data['start_at'] = date('Y-m', strtotime($data['start_at']));
                $data['end_at'] = date('Y-m', strtotime($data['end_at']));
                $query = $query->whereBetween('month', [$data['start_at'], $data['end_at']]);
            } else {
                if (isset($data['start_at'])) {
                    $data['start_at'] = date('Y-m', strtotime($data['start_at']));
                    $query = $query->where('month', $data['start_at']);
                } elseif (isset($data['end_at'])) {
                    $data['end_at'] = date('Y-m', strtotime($data['end_at']));
                    $query = $query->where('month', $data['end_at']);
                }
            }
        }
        if (isset($data['is_audit'])) {
            $query = $query->where('is_audit', 0);
        } else {
            if ($this->office === 1) {
                $query = $query->where('is_audit', '!=', 4);
            }
            if ($this->office === 2) {
                $query = $query->where('is_audit', 1);
            }
        }
        $ProjectSchedules = $query->whereIn('user_id', $this->seeIds);

        return $ProjectSchedules;
    }

    public function projectProgressList(Request $request)
    {
        $params = $request->all();
        $result = $this->projectProgressM($params);
        $ProjectSchedules = $result->orderBy('is_audit', 'desc')->get()->toArray();
        foreach ($ProjectSchedules as $k => $row) {
            $ProjectSchedules[$k]['money_from'] = Projects::where('id', $row['project_id'])->value('money_from');
            $Projects = Projects::where('id', $row['project_id'])->value('title');
            $ProjectSchedules[$k]['project_title'] = $Projects;
        }
        return response()->json(['result' => $ProjectSchedules], 200);
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
            rand(1000000, time()) . '_' . $params['project_num'] . '.' . $suffix
        );
        $path = 'storage/' . substr($path, 7);
        $img = Image::make($path);
        $img_w = $img->width();
        $img_h = $img->height();
        $img = $img->resize($img_w * 0.5, $img_h * 0.5)->save($path);
        $c = $img->response($suffix);

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
        $plans = DB::table('iba_project_plan')->where('date', $year)->where('project_id', $data['project_id'])->where('parent_id', 0)->first();

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
        unset(
            $data['id'], $data['updated_at'], $data['project_id'], $data['subject'], $data['project_num'],
            $data['build_start_at'], $data['build_end_at'], $data['total_investors'], $data['plan_start_at'],
            $data['plan_investors'], $data['plan_img_progress'], $data['month'], $data['project_title']
        );

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
        $data = $request->all();
        $result = ProjectSchedule::where('id', $data['id'])->update(['is_audit' => $data['status'], 'reason' => $data['reason']]);
        $projects = ProjectSchedule::where('id', $data['id'])->first();
        $year = (int)date('Y', strtotime($projects['month']));
        $month = (int)date('m', strtotime($projects['month']));
        $y = intval($year);
        $m = intval($month);
        $plans_amount_y = DB::table('iba_project_plan')->where('project_id', $projects['project_id'])->where('parent_id', 0)->where('date', $y)->value('id');
        $plans_amount = DB::table('iba_project_plan')->where('project_id', $projects['project_id'])->where('parent_id', $plans_amount_y)->where('date', $m)->value('amount');
        if ($data['status'] == 1) {
            $warResult = true;
            $warData = [];
            if ($plans_amount) {
                $Percentage = ($plans_amount - $projects['month_act_complete']) / $plans_amount;
                if ($Percentage > 0) {
                    if ($Percentage <= 0.1 && $Percentage > 0) {
                        $warData['warning_type'] = 0;
                    } elseif ($Percentage > 0.1 && $Percentage <= 0.2) {
                        $warData['warning_type'] = 1;
                    } elseif ($Percentage > 0.2) {
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
                'amount' => null,
                'image_progress' => '',
            ];
            $monthList = [];
            $ii = 0;
            foreach ($month as $k => $v) {
                $monthList[$ii] = [
                    'date' => (int)$v['month'],
                    'amount' => null,
                    'image_progress' => '',
                ];
                $ii++;
            }
            $data[$i]['month'] = $monthList;
            $i++;
        }

        return response()->json(['result' => $data], 200);
    }

    /**
     * 审核项目库信息
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function auditProject(Request $request)
    {
        $data = $request->input('params');
        $audited = Projects::where('id', $data['id'])->first()->audited;
        if ($audited !== 1) {
            if ($data['status'] === 1) {
                $audited = 1;
            }
        }
        $result = Projects::where('id', $data['id'])->update([
            'is_audit' => $data['status'],
            'reason' => $data['reason'],
            'audited' => $audited,
        ]);

        $result = $result || $result >= 0;

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

    public function toAudit(Request $request)
    {
        $id = $request->input('id');

        $result = Projects::where('id', $id)->update(['is_audit' => 0]);

        $result = $result ? true : false;

        return response()->json(['result' => $result], 200);
    }

    public function toAuditSchedule(Request $request)
    {
        $id = $request->input('id');

        $result = ProjectSchedule::where('id', $id)->update(['is_audit' => 0]);

        $result = $result ? true : false;

        return response()->json(['result' => $result], 200);
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
        if ($params['month']) {
            $month = date('Y-m', strtotime($params['month']));
            $year = date('Y', strtotime($params['month']));
        }
        $result = ProjectSchedule::where('project_id', $params['project_id'])->where('month', 'like', $year . '%')->sum('month_act_complete');
        $result = $result + $params['month_act_complete'];

        if ($params['type'] == 'edit') {
            $month_money = ProjectSchedule::where('project_id', $params['project_id'])->where('month', $month)->value('month_act_complete');
            $result = $result - $month_money;
        }
        return response()->json(['result' => $result], 200);
    }

    /**
     * 当月项目未填报列表
     *
     * @return JsonResponse
     */
    public function getProjectNoScheduleList()
    {
        $Project_id = ProjectSchedule::where('month', '=', date('Y-m'))->pluck('project_id')->toArray();
        $result = Projects::whereNotIn('id', $Project_id)->where('is_audit', 1)->get()->toArray();
        foreach ($result as $k => $val) {
            $users = User::select('username', 'phone')->where('id', $val['user_id'])->get()->toArray();
            $result[$k]['username'] = $users[0]['username'];
            $result[$k]['phone'] = $users[0]['phone'];
        }
        return response()->json(['result' => $result], 200);
    }

    /**
     * 当前项目当月是否填报
     *
     * @return JsonResponse
     */
    public function projectScheduleMonth(Request $request)
    {
        $params = $request->input();
        if (isset($params['project'])) {
            $month = ProjectSchedule::where('month', '=', date('Y-m'))
                ->where('project_id', $params['project'])
                ->value('month');
            $result = $month ? true : false;
        } else {
            $result = false;
        }
        return response()->json(['result' => $result], 200);
    }

    /**
     * 通知信息，获取未审核的填报信息和项目信息
     *
     * @return JsonResponse
     */
    public function noAudit()
    {
        $projectsQuery = new Projects();
        $scheduleQuery = new ProjectSchedule();

        $data['projects'] = $projectsQuery->whereIn('user_id', $this->seeIds)->where('is_audit', 0)->count();
        $data['schedule'] = $scheduleQuery->whereIn('user_id', $this->seeIds)->where('is_audit', 0)->count();

        return response()->json(['result' => $data], 200);
    }

    /**
     * 删除项目
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function projectDelete(Request $request)
    {
        $id = $request->input('id');
        if ($id) {
            $is_audit = Projects::where('id', $id)->value('is_audit');
            if ($is_audit === 4 || $is_audit === 2) {
                $result = Projects::where('id', $id)->delete();
                $planRes = ProjectPlan::where('project_id', $id)->delete();
                $result = $result ? true : false;
            } else {
                $result = false;
            }
            if ($result) {
                $log = new OperationLog();
                $log->eventLog($request, '删除项目');
            }
        } else {
            $result = false;
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 删除项目进度，填报
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function projectScheduleDelete(Request $request)
    {
        $id = $request->input('id');
        if ($id) {
            $is_audit = ProjectSchedule::where('id', $id)->value('is_audit');
            if ($is_audit === 4 || $is_audit === 2) {
                $result = ProjectSchedule::where('id', $id)->delete();
                $result = $result ? true : false;
            } else {
                $result = false;
            }
            if ($result) {
                $log = new OperationLog();
                $log->eventLog($request, '删除项目进度');
            }
        } else {
            $result = false;
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 转百度坐标
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function locationPosition(Request $request)
    {
        $position = $request->input('position');
        $center_url = 'http://api.map.baidu.com/geoconv/v1/?coords=' . $position['center'] . '&from=1&to=5&ak=ULD7Bs841R1vy18i61u2CnCRP65wlKRv';
        $center_data = file_get_contents($center_url);
        $center_data = str_replace('renderOption&&renderOption(', '', $center_data);
        $center_data = str_replace(')', '', $center_data);
        $positions_url = 'http://api.map.baidu.com/geoconv/v1/?coords=' . $position['positions'] . '&from=1&to=5&ak=ULD7Bs841R1vy18i61u2CnCRP65wlKRv';
        $positions_data = file_get_contents($positions_url);
        $positions_data = str_replace('renderOption&&renderOption(', '', $positions_data);
        $positions_data = str_replace(')', '', $positions_data);
        $data['center'] = json_decode($center_data, true);
        $data['positions'] = json_decode($positions_data, true);
        $result = [];
        foreach ($data['center']['result'] as $k => $v) {
            $result['center'][$k]['lng'] = $v['x'];
            $result['center'][$k]['lat'] = $v['y'];
        }
        foreach ($data['positions']['result'] as $k => $v) {
            $result['positions'][$k]['lng'] = $v['x'];
            $result['positions'][$k]['lat'] = $v['y'];
        }
        return response()->json(['result' => $result], 200);
    }

}
