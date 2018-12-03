<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RegistController extends Controller
{
    public function getUsers()
    {
        $data = DB::table('users')->select('name', 'username', 'email', 'created_at')->get()->toArray();

        return response()->json(['result' => $data], 200);
    }

    public function registUser(Request $request)
    {
        $data = $request->input();
        unset($data['pwdCheck']);
        $data['password'] = bcrypt($data['password']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $result = DB::table('users')->insert($data);
        if ($result) {
            return response()->json(['result' => true], 200);
        } else {
            return response()->json(['result' => false], 200);
        }
    }
}
