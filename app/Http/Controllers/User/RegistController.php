<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Departments;
use App\Models\Role;
use App\Models\OperationLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistController extends Controller
{
    /**
     * 获取用户列表
     *
     * @return JsonResponse
     */
    public function getUsers()
    {
        $data = DB::table('users')->select('name', 'username', 'email', 'created_at', 'department_id', 'last_login', 'group_id')->get()->toArray();

        foreach ($data as $k => $row) {
            if (!($row['department_id'])) {
                $data[$k]['department'] = '无';
            } else {
                $department = Departments::where('id', $row['department_id'])->first()->title;
                $data[$k]['department'] = $department;
            }
            unset($data[$k]['department_id']);

            if (!($row['group_id'])) {
                $data[$k]['group'] = '无';
            } else {
                $group = Role::where('id', $row['group_id'])->first()->name;
                $data[$k]['group'] = $group;
            }
            unset($data[$k]['group_id']);
        }

        return response()->json(['result' => $data], 200);
    }

    /**
     * 创建用户
     *
     * @param Request $request
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
     * @param Request $request
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
}
