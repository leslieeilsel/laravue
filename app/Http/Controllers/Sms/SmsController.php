<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\PhoneLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function send(Request $request)
    {
        $phone = $request->input('phone');
        $code = random_int(1000, 9999);
        $post_data = [
            'userid' => 3724,
            'account' => '106-dxhm',
            'password' => '888888',
            'content' => '您的验证码为 '.$code.'【电信惠民】',
            'mobile' => $phone,
            'sendtime' => '',
        ];

        $url = 'http://115.28.179.225:8888/sms.aspx?action=send';
        $o = '';
        foreach ($post_data as $k => $v) {
            $o .= "$k=".urlencode($v).'&';
        }
        $post_data = substr($o, 0, -1);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
        $result = curl_exec($ch);
        $xml = simplexml_load_string($result);

        return response()->formatJson(['xml' => $xml, 'code' => $code]);
    }

    public function savePhone(Request $request) {
        $params = $request->all();
        $params['verified_at'] = Carbon::now();
        $phoneLog = new PhoneLog($params);
        $result = $phoneLog->save();

        return response()->formatJson(['result' => $result]);
    }
}
