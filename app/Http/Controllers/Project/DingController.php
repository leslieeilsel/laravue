<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use function GuzzleHttp\json_encode;

class DingController extends Controller
{
    public function getToken(){
        $appKey=env("Ding_App_Key");
        $appSecret=env("Ding_App_Secret");
        $accessToken=Cache::get('dingAccessToken');
        $url='https://oapi.dingtalk.com/message/send_to_conversation';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $post_data = array(
            "access_token" => $accessToken,
            "sender" => "12345"
            );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $json =  curl_exec($ch);
        curl_close($ch);
        $arr=json_decode($json,true);
        dd($arr);
    }
    public function userNotify(){
        $appKey=env("Ding_App_Key");
        $appSecret=env("Ding_App_Secret");
        $accessToken=Cache::get('dingAccessToken');
        $signInfo=$this->sign();
        $url='https://oapi.dingtalk.com/sns/getuserinfo_bycode';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        //设置post数据
        $post_data = array(
            "accessKey" => $signInfo['appId'],
            "timestamp" => $signInfo['time'],
            "signature"=>$signInfo['sign']
            );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        // $arr=json_decode($json,true);
        // dd($arr);
        return json_encode($post_data);
    }
    public function sign(){
        $appSecret=env("Ding_App_Secret");
        $appId='dingq5pc0ffixdxmkpwt';
        $time=$this->getMillisecond();
        $s = hash_hmac('sha256', $time , $appSecret, true);
        $signature = base64_encode($s);
        $urlencode_signature = urlencode($signature);
        return ['appId'=>$appId,'time'=>$time,'sign'=>$urlencode_signature];
    }
    // 毫秒级时间戳
    public function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }
}
                     