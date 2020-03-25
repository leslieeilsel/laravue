<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Departments;
use App\Models\Dict;
use App\Models\OperationLog;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegistController extends Controller
{
    /**
     * 获取用户列表
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function getUsers(Request $request)
    {
        $params = $countParams = $request->input();
        unset($countParams['pageNumber'], $countParams['pageSize']);
        $data = $this->getUsersByParams($params)->get()->toArray();

        $office = Dict::getOptionsArrByName('职位');
        foreach ($data as $k => $row) {
            $data[$k]['department_title'] = isset($row['department_id'])
                ? Departments::where('id', $row['department_id'])->first()->title
                : '无';

            $data[$k]['group'] = isset($row['group_id'])
                ? Role::where('id', $row['group_id'])->first()->name
                : '无';

            $data[$k]['office_name'] = isset($row['office']) ? $office[$row['office']] : '无';
        }

        $count = $this->getUsersByParams($countParams)->count();

        return response()->json(['result' => $data, 'total' => $count], 200);
    }

    public function getUsersByParams($params)
    {
        $query = DB::table('users')
            ->select('id', 'name', 'username', 'email', 'created_at', 'department_id', 'last_login', 'group_id',
                'office', 'phone');

        if (isset($params['department_id'])) {
            $query = $query->where('department_id', $params['department_id']);
        }
        if (isset($params['username'])) {
            $query = $query->where('username', 'like', '%'.$params['username'].'%');
        }
        if (isset($params['name'])) {
            $query = $query->where('name', 'like', '%'.$params['name'].'%');
        }
        if (isset($params['group_id']) && $params['group_id'] !== -1) {
            $query = $query->where('group_id', $params['group_id']);
        }
        if (isset($params['pageNumber'], $params['pageSize'])) {
            $query
                ->limit($params['pageSize'])
                ->offset(($params['pageNumber'] - 1) * $params['pageSize']);
        }

        return $query;
    }

    /**
     * 创建用户
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function registUser(Request $request)
    {
        $data = $request->input();
        unset($data['pwdCheck'], $data['department_title']);
        $data['password'] = bcrypt($data['password']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $result = DB::table('users')->insert($data);

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '创建用户');
        }

        return $result ? response()->json(['result' => true], 200) : response()->json(['result' => false], 200);
    }

    /**
     * 修改密码
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $oldPassword = $request->get('oldPassword');

        $user = Auth::user();
        if (Hash::check($oldPassword, $user->password)) {
            $newPassword = $request->get('newPassword');
            $result = DB::table('users')->where('id', $user->id)->update(['password' => bcrypt($newPassword)]);
            $result = $result ? true : false;
        } else {
            $result = false;
        }

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '修改密码');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 获取数据字典数据
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function getUserDictData(Request $request)
    {
        $nameArr = $request->input('dictName');
        $result = $this->getDictDataByName($nameArr);

        return response()->json(['result' => $result], 200);
    }

    /**
     * 获取数据字典数据
     *
     * @param $nameArr
     *
     * @return array
     */
    public static function getDictDataByName($nameArr)
    {
        $data = [];

        if ($nameArr) {
            foreach ($nameArr as $key => $name) {
                $data[$key] = Dict::getOptionsByName($name);
            }
        }

        return $data;
    }

    /**
     * 删除用户
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function deleteUserData(Request $request)
    {
        $ids = $request->input('id');
        if ($ids) {
            $result = DB::table('users')->whereIn('id', explode(',', $ids))->delete();
            $result = $result ? true : false;
        } else {
            $result = false;
        }

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '删除用户');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 修改用户
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function editRegistUser(Request $request)
    {
        $params = $request->input();
        unset($params['department_title']);
        $params['updated_at'] = date('Y-m-d H:i:s');
        $id = $params['id'];

        if (isset($params['password'])) {
            $params['password'] = bcrypt($params['password']);
        } else {
            unset($params['password']);
        }
        unset($params['id'], $params['department_title'], $params['group'], $params['office_name'], $params['_index'], $params['_rowKey'], $params['pwdCheck']);
        $result = DB::table('users')->where('id', $id)->update($params);

        $result = $result > 0;

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '修改用户');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 获取单挑用户
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function getUser(Request $request)
    {
        $params = $request->input();
        $result = DB::table('users')->where('id', $params['id'])->first();

        return response()->json(['result' => $result], 200);
    }
}
