<?php

namespace App\Http\Controllers\System;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController
{
    /**
     * 添加角色
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        $data = $request->input();
        $data['created_at'] = date('Y-m-d H:i:s');
        $result = Role::insert($data);

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

        return response()->json(['result' => $roles], 200);
    }
}
