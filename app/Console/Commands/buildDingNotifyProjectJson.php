<?php

namespace App\Console\Commands;

use App\Models\Dict;
use App\Models\Project\Projects;
use App\Models\Project\ProjectSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use DB;

class buildDingNotifyProjectJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buildDingNotifyProjectJson';

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
        $agent_id=env("Ding_Agent_Id");
        $accessToken=Cache::get('dingAccessToken');
        if(!$accessToken){
            $this->getToken();
            $accessToken=Cache::get('dingAccessToken');
        }
        $url='https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token='.$accessToken;
        $user=$this->getProjectNoScheduleList();
        $post_data=array(
            'agent_id'=>$agent_id,
            'userid_list'=>$user,
            'msg'=>json_encode([
                "msgtype"=>"您的项目还没填报",
                "text"=>["content"=>"您的项目还没填报"]
            ])
        );
        $json=$this->postCurl($url,$post_data,'post');
        return $json;
    }
    /**
     * 当月项目未填报列表
     *
     * @return JsonResponse
     */
    public function getProjectNoScheduleList()
    {
        $Project_id = ProjectSchedule::where('month', '=', date('Y-m'))->pluck('project_id')->toArray();
        $result = Projects::whereNotIn('id', $Project_id)->where('is_audit', 1)->get()->toArray();
        $str='';
        foreach ($result as $val) {
            $user=DB::table('users')->where('id', $val['user_id'])->value('ding_user_id');
            $str = $str.','.$user;
        }
        return substr($str,1);
    }
}
