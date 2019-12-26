<?php

namespace App\Http\Controllers\Index;


use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Dict;
use Illuminate\Support\Facades\Mail;

class IndexController extends Controller
{
    public function __construct()
    {
        //完全跨域设置 包括跨域名和端口
        header('Access-Control-Allow-Origin:*');
        header("Access-Control-Allow-Headers:*"); //跨端口
        header("Access-Control-Allow-Methods:GET, POST, PUT, DELETE, OPTIONS"); //允许的请求类型
        header('Content-Type:text/plain');
    }
    /**
     * 获取项目库表单中的数据字典数据多个
     *
     * @param Request $request
     * @return array
     */
    public function dictData(Request $request)
    {
        $nameArr = $request->input("dictName");
        $dataArr = [];
        if ($nameArr) {
            foreach ($nameArr as $field => $name) {
                $category = DB::table('iba_system_dict')->where('title', $name)->first();
                $data = $category? DB::table('iba_system_dict_data')->where('dict_id',$category['id'])->pluck('title', 'value')->toArray() : [];
                if ($data) {
                    $i = 0;
                    foreach ($data as $value => $title) {
                        $data[$i] = [
                            'title' => $title,
                            'value' => $value
                        ];
                        $i++;
                    }
                }
                $dataArr[$field] = $data;
            }
        }
        return response()->json(['result' => $dataArr], 200);
    }
    //登陆
    public function login(Request $request)
    {
        $params = $request->input();
        if(isset($params['username'])){
            $user_data=DB::table('users')->where('username',$params['username'])->first();
            $result=[];
            if ($user_data && isset($params['password'])) {
                if(password_verify($params['password'],$user_data['password'])){
                    $result['code']=200;
                    $result['message']='登陆成功';
                }else{
                    $result['code']=300;
                    $result['message']='用户名或密码不正确';
                }
            }else{
                $result['code']=300;
                $result['message']='用户名或密码不正确';
            }
            
            DB::table('users')->update(['last_login'=>date('Y-m-d H:i:s')]);
        }else{
            $result['code']=300;
            $result['message']='请填写用户名或密码';
        }
        return response()->json($result, 200);
    }
    //注册
    public function regist(Request $request)
    {
        $params = $request->input();
        if(isset($params['username'])&&isset($params['password'])){
            $user_data=DB::table('users')->where('username',$params['username'])->value('id');
            if($user_data){
                $result['code']=300;
                $result['message']='用户名已存在请重新注册';            
            }else{
                $params['password'] = bcrypt($params['password']);
                $params['created_at'] = date('Y-m-d H:i:s');
                $data = DB::table('users')->insert($params);
                if($data){
                    $result['code']=200;
                    $result['message']='注册成功';  
                }else{
                    $result['code']=300;
                    $result['message']='用户注册失败，请重新注册';
                }
            }
        }else{
            $result['code']=300;
            $result['message']='请填写用户名或密码';
        }
        return response()->json($result, 200);
    }
    //用户信息
    public function userInfo(Request $request)
    {
        $params = $request->input();
        if(isset($params['username'])){
            $user_data=DB::table('users')->where('username',$params['username'])->first();
            $user_attr = Dict::getOptionsArrByName('用户属性');
            if(isset($user_data['user_attr'])){
                $user_data['user_attr']=$user_attr[$user_data['user_attr']];
            }
            if($user_data){
                $result['code']=200;
                $result['message']='成功';
                $result['result']=$user_data;             
            }else{
                $result['code']=300;
                $result['message']='用户不存在，没有相关信息';
            }
        }else{
            $result['code']=300;
            $result['message']='获取不到当前用户，请刷新页面重新获取';
        }
        return response()->json($result, 200);
    }
    //修改资料
    public function registUpdate(Request $request)
    {
        $params = $request->input();
        if(isset($params['username'])){
            $params['updated_at'] = date('Y-m-d H:i:s');
            $user_data=DB::table('users')->where('username',$params['username'])->update($params);
            if($user_data){
                $result['code']=200;
                $result['message']='资料修改成功';  
            }else{
                $result['code']=300;
                $result['message']='资料修改成功，请重新修改';
            }
        }else{
            $result['code']=300;
            $result['message']='获取用户出错';
        }

        return response()->json($result, 200);
    }
    //申请
    public function application(Request $request)
    {
        $params = $request->input();
        if(isset($params['username'])){
            $user_data=DB::table('users')->where('username',$params['username'])->value('id');
            if($user_data){
                $params['user_id']=$user_data;
                $params['audit_state']=0;
                $params['created_at']=date('Y-m-d H:i:s');
                unset($params['username']);
                $data = DB::table('application')->insert($params);
                if($data){
                    $result['code']=200;
                    $result['message']='申请成功';  
                }else{
                    $result['code']=300;
                    $result['message']='用户申请失败，请重新申请';
                }
            }else{
                $result['code']=300;
                $result['message']='用户不存在';
            }
        }else{
            $result['code']=300;
            $result['message']='获取用户出错';
        }

        return response()->json($result, 200);
    }
    //申请列表
    public function applicationList(Request $request)
    {
        $params = $request->input();
        $data = DB::table('application');
        if(isset($params['username'])){
            $user_data=DB::table('users')->where('username',$params['username'])->value('id');
            $data->where('user_id',$user_data);
        }
        $result = $data->get()->toArray();
        $resources = Dict::getOptionsArrByName('申请资源');
        $type = Dict::getOptionsArrByName('申请类型');
        $cloud_type = Dict::getOptionsArrByName('云平台类型');
        $AI_type = Dict::getOptionsArrByName('人工智能类型');
        $hart_type = Dict::getOptionsArrByName('硬科技类型');
        $audit_state = Dict::getOptionsArrByName('申请审核状态');
        foreach ($result as $k => $row) {
            
            if(isset($row['resources'])){
                $result[$k]['resources'] = $resources[$row['resources']];
            }
            if(isset($row['type'])){
                $result[$k]['type'] = $type[$row['type']];
            }
            if(isset($row['cloud_type'])){
                $result[$k]['cloud_type'] = $cloud_type[$row['cloud_type']];
            }
            if(isset($row['AI_type'])){
                $result[$k]['AI_type'] = $AI_type[$row['AI_type']];
            }
            if(isset($row['hart_type'])){
                $result[$k]['hart_type'] = $hart_type[$row['hart_type']];
            }
            if(isset($row['audit_state'])){
                $result[$k]['audit_state'] = $audit_state[$row['audit_state']];
            }
        }
        return response()->json($result, 200);
    }
    //申请审核
    public function applicationUpdate(Request $request)
    {
        $params = $request->input();
        $id=$params['id'];
        unset($params['id']);
        $data = DB::table('application')->where('id',$id)->update($params);
        if($data){
            $result['code']=200;
            $result['message']='提交申请成功';  
        }else{
            $result['code']=300;
            $result['message']='提交申请失败，请重新提交申请';
        }

        return response()->json($result, 200);
    }
    
    //检索
    public function searchTitle(Request $request)
    {
        $params = $request->input();
        if(isset($params['title'])){
            $result = DB::table('search_title')->where('title', 'like','%'.$params['title'].'%')->get()->toArray();
        }else{
            $result = DB::table('search_title')->get()->toArray();
        }

        return response()->json($result, 200);
    }
    //修改密码发送邮件
    public function updatePwdMail(Request $request)
    {
        $params = $request->input();
        
        if(isset($params['email'])){
            $id = DB::table('users')->where('email',$params['email'])->value('id');
            if($id){
                $verify_code=rand(0,999999);
                Mail::raw('你好，您的验证码是'.$verify_code, function ($message)use($request) {
                    $to=$request->input('email');
                    $message ->to($to)->subject('【硬科技】忘记密码验证码');
                });
                
                if(count(Mail::failures()) < 1){
                    $result['code']=200;
                    $result['message']='发送邮件成功，请查收！';
                    $result['verify_code']=$verify_code;
                }else{
                    $result['code']=300;
                    $result['message']='发送邮件失败，请重试！';
                } 
            }else{
                $result['code']=300;
                $result['message']='没有当前用户';
            }
        }else{
            $result['code']=300;
            $result['message']='你的邮箱未获取，请重新提交';
        }

        return response()->json($result, 200);
    }
    //修改密码发送邮件
    public function updatePwd(Request $request)
    {
        $params = $request->input();
        if(isset($params['email'])&&isset($params['password'])){
            $id = DB::table('users')->where('email',$params['email'])->update(['password'=>bcrypt($params['password'])]);
            if($id){
                $result['code']=200;
                $result['message']='密码修改成功';
            }else{
                $result['code']=300;
                $result['message']='密码修改失败！';
            } 
        }else{
            $result['code']=300;
            $result['message']='请填写邮箱或密码';
        }

        return response()->json($result, 200);
    }
}
                     