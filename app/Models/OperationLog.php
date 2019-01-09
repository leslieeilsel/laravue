<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OperationLog extends Model
{
    public $timestamps = true;
    
    protected $table = 'operation_log';

    public function eventLog($request, $active)
    {
        $user_id = 0;
        if (Auth::check()) {
            $user_id = (int) Auth::id();
        }
        if ('GET' != $request->method()) {
            $data = [
                'title' => $active,
                'user_id' => $user_id,
                'method' => $request->method(),
                'path' => $request->path(),
                'ip' => $request->ip(),
                'ip_place' => '',
                'created_at' => date('Y-m-d H:i:s')
            ];
            $result = self::insert($data);
        }
    }
    
    /*把IP传入新浪API返回数据获取ip的真实归属地*/
    // public function getIpPlace($ip = '')
    // {
    //     $res = @file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip);

    //     return $res;
    // }
}
