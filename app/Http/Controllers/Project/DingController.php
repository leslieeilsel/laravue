<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use function GuzzleHttp\json_encode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DingController extends Controller
{
    public function getToken(){
        $appKey=env("Ding_App_Key");
        $appSecret=env("Ding_App_Secret");
        // $accessToken=Cache::get('dingAccessToken');
        // $url='https://oapi.dingtalk.com/message/send_to_conversation';
        // $ch = curl_init();
        // $post_data = array(
        //     "access_token" => $accessToken,
        //     "sender" => "12345"
        //     );
        // $json=$this->postCurl($url,$post_data);
        // $arr=json_decode($json,true);
        
        $url='https://oapi.dingtalk.com/gettoken?appkey='.$appKey.'&appsecret='.$appSecret;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        $arr=json_decode($json,true);
        Cache::put('dingAccessToken', $arr['access_token'], 7200);
        dd($arr);
    }
    public function userNotify(Request $request){
        $data = $request->all();
        $appKey=env("Ding_App_Key");
        $appSecret=env("Ding_App_Secret");
        $accessToken=Cache::get('dingAccessToken');
        $url='https://oapi.dingtalk.com/sns/getuserinfo?access_token=4b393f16f9f03217bed31fbcd045d9bb&code='.$data['code'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        // $arr=json_decode($json,true);
        // $post_data = array(
        //     "accessKey" => $accessToken,
        //     "timestamp" => $this->getMillisecond(),
        //     "signature"=>$signInfo['sign']
        // );
        // $arr=json_decode($json,true);
        // dd($arr);
        return $json;
    }
    public function sign(){
        $appSecret=env("Ding_App_Secret");
        $appId='dingq5pc0ffixdxmkpwt';
        $time=$this->getMillisecond();
        $s = hash_hmac('sha256', $time , $appSecret, true);
        $signature = base64_encode($s);
        $urlencode_signature = urlencode($signature);
        return ['appId'=>$appId,'time'=>$time,'sign'=>$urlencode_signature];
        // return $urlencode_signature;
    }
    // 毫秒级时间戳
    public function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }
    // curl
    public function postCurl($url,$data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        //设置post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        return $json;
    }
}
                     