<?php

namespace App\Http\Controllers\Project;

use App\Models\Dict;
use App\Models\Project\ProjectPlan;
use App\Models\Project\Projects;
use App\Models\Project\ProjectSchedule;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectMapController extends Controller
{
    public $user;
    public $office;
    public $group_id;

    public function __construct()
    {
        if (Auth::check()) {
            $this->user = Auth::user();
            $this->office = $this->user->office;
            $this->group_id = $this->user->group_id;
        }
    }

    /**
     * 可以查看项目的ID集合
     *
     * @return array
     */
    public function getSeeIds()
    {
        $seeIds = [];

        $roleId = $this->group_id;
        $userId = $this->user->id;
        $users = User::all()->toArray();
        // 数据权限类型
        $dataType = Role::where('id', $roleId)->first()->data_type;

        if ($dataType === 0) {
            $seeIds = array_column($users, 'id');
        }
        if ($dataType === 1) {
            $departmentIds = DB::table('iba_role_department')->where('role_id', $roleId)->get()->toArray();
            $departmentIds = array_column($departmentIds, 'department_id');
            $userIds = collect($users)->whereIn('department_id', $departmentIds)->all();
            $seeIds = array_column($userIds, 'id');
        }
        if ($dataType === 2) {
            $seeIds = [$userId];
        }

        return $seeIds;
    }

    public function getMapProjects(Request $request)
    {
        $params = $request->all();
        $data = $this->getMapProjectsData($params);

        $date = ProjectSchedule::select('month')->groupBy('month')->orderBy('month', 'desc')->value('month');
        $dateArr = explode('-', $date);
        $year = (int) $dateArr[0];
        $month = (int) $dateArr[1];

        $type = Dict::getOptionsArrByName('工程类项目分类');
        $status = Dict::getOptionsArrByName('项目状态');
        foreach ($data as $k => $project) {
            $parentPlanId = ProjectPlan::where('project_id', $project['id'])->where('date', $year)->first()->id;
            $planMonth = ProjectPlan::where('project_id', $project['id'])->where('date', $month)->where('parent_id', $parentPlanId)->first();
            $planAmount = $planMonth['amount'] ? (float) $planMonth['amount'] : 0;
            $actMonth = ProjectSchedule::where('project_id', $project['id'])->where('month', $date)->first();
            $actAmount = $actMonth['month_act_complete'] ?  (float) $actMonth['month_act_complete'] : 0;

            $percent = 0;
            if ($actAmount != 0 && $planAmount != 0) {
                $percent = $actAmount / $planAmount;
            } elseif ($actAmount != 0 && $planAmount == 0) {
                $percent = 1;
            } elseif ($actAmount == 0 && $planAmount != 0) {
                $percent = 0;
            } elseif ($actAmount == 0 && $planAmount == 0) {
                $percent = 1;
            }

            $data[$k]['planAmount'] = $planAmount;
            $data[$k]['actAmount'] = $actAmount;
            $data[$k]['percent'] = $percent;
            $data[$k]['type'] = $type[$project['type']];
            $data[$k]['status'] = $status[$project['status']];
        }

        return response()->json(['result' => $data], 200);
    }

    public function getMapProjectsData($params)
    {
        $query = new Projects();

        $query = $query->select('id', 'title', 'status', 'description', 'type', 'is_audit', 'positions', 'center_point');

        if (isset($params['department_id'])) {
            if (gettype($params['department_id']) == 'string') {
                $params['department_id'] = explode(',', $params['department_id']);
            }
            $departmentCount = count($params['department_id']) - 1;
            if (count($params['department_id']) > 0) {
                $user_ids = DB::table('users')->select('id')->where('department_id', $params['department_id'][$departmentCount])->get()->toArray();
                $user_id = array_column($user_ids, 'id');
                $query = $query->whereIn('user_id', $user_id);
            }
        }
        if (isset($params['title'])) {
            $query = $query->where('title', 'like', '%' . $params['title'] . '%');
        }
        if (isset($params['subject'])) {
            $query = $query->where('subject', 'like', '%' . $params['subject'] . '%');
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
        /*
         * id_audit
         * 0 => 待审核
         * 1 => 审核通过
         * 2 => 审核不通过
         * 3 => 投资调整
         * 4 => 待提交
         */
        $query = $query->whereIn('is_audit', [1, 3]);

        $seeIds = $this->getSeeIds();
        $query = $query->whereIn('user_id', $seeIds);

        $projects = $query->get()->toArray();

        return $projects;
    }
}
