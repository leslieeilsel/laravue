<?php

namespace App\Http\Controllers\System;

use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OperationLog;
use App\Models\Departments;

class RoleController
{
    /**
     * 创建角色
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $data = $request->input();
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = DB::table('ibiart_slms_roles')->insertGetId($data);

        $result = $id ? Role::where('id', '!=', $id)->update(['is_default' => 0]) : false;

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '创建角色');
        }

        return $result ? response()->json(['result' => true], 200) : response()->json(['result' => false], 200);
    }

    /**
     * 获取权限列表
     *
     * @return JsonResponse
     */
    public function getRoles()
    {
        $roles = Role::all()->toArray();
        $is_loading = [
            'is_loading' => false,
        ];

        array_walk($roles, function (&$value, $key, $is_loading) {
            $value = array_merge($value, $is_loading);
        }, $is_loading);

        return response()->json(['result' => $roles], 200);
    }

    public function setRoleMenus(Request $request)
    {
        $insertArr = [];
        $roleId = $request->input('roleid');

        $selected = $request->input('selected');
        foreach ($selected as $k => $row) {
            $insertArr[] = [
                'role_id' => $roleId,
                'menu_id' => $row['id'],
                'checked' => $row['checked'],
            ];
        }

        DB::table('ibiart_slms_role_menus')->where('role_id', $roleId)->delete();
        $result = DB::table('ibiart_slms_role_menus')->insert($insertArr);

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '设置菜单权限');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 设置默认角色
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setDefaultRole(Request $request)
    {
        $data = $request->input();

        $setDefaultRes = Role::where('id', $data['id'])->update(['is_default' => $data['is_default']]);

        if (!$setDefaultRes) {
            $result = false;
        } else {
            $result = Role::where('id', '!=', $data['id'])->update(['is_default' => 0]);
            $result = $result ? true : false;
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 获取部门树
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDepartmentTree(Request $request)
    {
        $roleId = $request->input('roleId');
        $data = $this->buildDepartmentTree(0, $roleId);

        return response()->json(['result' => $data], 200);
    }

    /**
     * 组织部门树
     *
     * @param $parent_id
     * @param $roleId
     * @return array
     */
    public function buildDepartmentTree($parent_id, $roleId)
    {
        $childTree = [];
        $childDepartment = Departments::where('parent_id', $parent_id)->get()->toArray();
        // 匹配子记录
        foreach ($childDepartment as $k => $v) {
            if ($roleId) {
                $res = DB::table('iba_role_department')->where('role_id', $roleId)->where('department_id', $v['id'])->first();
                if ($res) {
                    $v['checked'] = true;
                }
            }
            $v['children'] = $this->buildDepartmentTree($v['id'], $roleId); // 递归获取子记录
            if ($v['children'] === null) {
                unset($v['children']);                          // 如果子元素为空则unset()
            }
            $v['key'] = $v['id'];
            $v['expand'] = true;
            $childTree[] = $v;
        }

        return $childTree;
    }

    /**
     * 编辑数据权限
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function editRoleDep(Request $request)
    {
        $params = $request->input('params');

        $roleId = $params['roleId'];
        $dataType = $params['dataType'];
        $deptIds = $params['depIds'];

        $res = Role::where('id', $roleId)->update(['data_type' => $dataType]);

        $update_res = true;
        // 自定义数据权限需要更新数据
        if ($dataType === 1) {
            $deptArr = explode(',', $deptIds);
            $insertArr = [];
            foreach ($deptArr as $k => $v) {
                $insertArr[$k]['role_id'] = $roleId;
                $insertArr[$k]['department_id'] = (int)$v;
            }

            $del_res = DB::table('iba_role_department')->where('role_id', $roleId)->delete();
            if ($del_res >= 0) {
                $update_res = DB::table('iba_role_department')->insert($insertArr);
            }
        }

        $result = ($res >= 0 && $update_res === true) ? true : false;

        return response()->json(['result' => $result], 200);
    }
}
