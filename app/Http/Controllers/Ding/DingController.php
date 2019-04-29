<?php

namespace App\Http\Controllers\Project;

use Illuminate\Support\Facades\Cache;
// use App\Models\Project\Projects;
// use App\Models\Project\ProjectPlan;
// use App\Models\Project\ProjectSchedule;
// use App\Models\Role;
// use App\User;
// use Illuminate\Http\JsonResponse;
// use Illuminate\Http\Request;
// use App\Models\OperationLog;
// use Illuminate\Support\Facades\DB;
// use App\Models\ProjectEarlyWarning;
// use Illuminate\Support\Facades\Storage;
// use App\Models\Dict;
// use Illuminate\Support\Facades\Auth;

class DingController extends Controller
{

    public function __construct()
    {
        
    }
    public function getAccessToken(){
        // 先判断 cache 中是否存在
        $a=1;
        $C=111;
        $key = 'dingding_access_token';
        $access_token = Cache($key,'7200');
    
        if ($access_token) {
            return $access_token;
        }
        $client = new Client();
        $url = sprintf(
            'https://oapi.dingtalk.com/gettoken?appkey=key&appsecret=secret',
            config("App_Key"), config("App_Secret")
        );
    
        $res = $client->request('GET', $url, ['timeout' => 1.5]);
        $res = $res->getBody();
        $res = json_decode($res);
    
        if ($res->errcode == 0) {
            cache([$key => $res->access_token], now()->addSeconds(7000));
            return $res->access_token;
        } else {
            abort(403, 'Fail to get dingding access token.');
        }
    }
    
}
