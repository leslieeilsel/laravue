<?php

namespace App\Http\Controllers\System;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OperationLog;

class RoleController
{
    /**
     * 创建角色
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        $data = $request->input();
        $data['created_at'] = date('Y-m-d H:i:s');
        $result = Role::insert($data);

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '创建角色');
        }

        return $result ? response()->json(['result' => true], 200) : response()->json(['result' => false], 200);
    }

    /**
     * 获取权限列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoles()
    {
        $roles = Role::select('id', 'name', 'description', 'created_at')->get()->toArray();
        $is_loading = [
            'is_loading' => false
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
                'checked' => $row['checked']
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
}
