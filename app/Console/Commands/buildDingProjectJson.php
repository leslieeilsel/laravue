<?php

namespace App\Console\Commands;

use App\Models\Dict;
use App\Models\Project\Projects;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class buildDingProjectJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buildDingProjectJson';

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
        $appKey=env("Ding_App_Key");
        $appSecret=env("Ding_App_Secret");
        $url='https://oapi.dingtalk.com/gettoken?appkey='.$appKey.'&appsecret='.$appSecret;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        $arr=json_decode($json,1);
        file_put_contents('11.txt',$arr);
        config(["auth.Ding_Access_Token"=>$arr['access_token']]);
    }
}
