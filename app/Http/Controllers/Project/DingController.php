<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
class DingController extends Controller
{
    public function getToken(){
        // $appKey=env("Ding_App_Key");
        // $appSecret=env("Ding_App_Secret");
        // $url='https://oapi.dingtalk.com/gettoken?appkey='.$appKey.'&appsecret='.$appSecret;
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // curl_setopt($ch, CURLOPT_URL, $url);
        // $json =  curl_exec($ch);
        // curl_close($ch);
        // $arr=json_decode($json,1);
        // config(["auth.Ding_Access_Token"=>$arr['access_token']]);
        dd(Cache::put('dingAccessToken'));
    }
    
}
