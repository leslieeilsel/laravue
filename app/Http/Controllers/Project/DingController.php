<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use function GuzzleHttp\json_encode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use function GuzzleHttp\json_decode;
use App\User;

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
        dd($arr['access_token']);
    }
    public function userId(Request $request){
        $data = $request->all();
        $appKey=env("Ding_App_Key");
        $appSecret=env("Ding_App_Secret");
        $accessToken=Cache::get('dingAccessToken');
        if(!$accessToken){
            $this->getToken();
            $accessToken=Cache::get('dingAccessToken');
        }
        $user_id_url='https://oapi.dingtalk.com/user/getuserinfo?access_token='.$accessToken.'&code='.$data['code'];
        $user_ids=$this->postCurl($user_id_url,[],'get');
        $user_id=json_decode($user_ids,true);
        $url='https://oapi.dingtalk.com/user/get?access_token='.$accessToken.'&userid='.$user_id['userid'];
        $json=$this->postCurl($url,[],'get');
        $arr=json_decode($json,true);

        $result = DB::table('users')->where('phone', $arr['mobile'])->update('ding_user_id',$arr['userid']);
        return $json;
    }
    public function userNotify(){
        $appKey=env("Ding_App_Key");
        $appSecret=env("Ding_App_Secret");
        $agent_id=env("Ding_Agent_Id");
        $accessToken=Cache::get('dingAccessToken');
        if(!$accessToken){
            $this->getToken();
            $accessToken=Cache::get('dingAccessToken');
        }
        $url='https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token='.$accessToken;
        
        $post_data=array(
            'agent_id'=>$agent_id,
            'userid_list'=>'0362614366942884',
            'msg'=>json_encode([
                "msgtype"=>"text",
                "text"=>["content"=>"张三的请假申请"]
            ])
        );
        $json=$this->postCurl($url,$post_data,'post');
        return $json;
    }
    public function sign(){
        $appSecret=env("Ding_App_Secret");
        $appId=env("Ding_App_Key");
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
    public function postCurl($url,$data,$type) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if($type=='post'){
            curl_setopt($ch, CURLOPT_POST, TRUE);
            //设置post数据
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        return $json;
    }
}
                     