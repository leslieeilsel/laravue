<?php

namespace App\Console\Commands;

use App\Models\Dict;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class buildDingTokenActivityJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buildDingTokenActivityJson';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /** 
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    public function handle(){
        $appKey=env("Ding_activity_App_Key");
        $appSecret=env("Ding_activity_App_Secret");
        $url='https://oapi.dingtalk.com/gettoken?appkey='.$appKey.'&appsecret='.$appSecret;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        $arr=json_decode($json,true);
        Cache::put('dingActivityAccessToken', $arr['access_token'], 7200);
        $str=var_export($arr,TRUE);
        file_put_contents('22.txt',$str."***".Cache("dingActivityAccessToken").date('Y-m-d H:i:s'));
    }
}
