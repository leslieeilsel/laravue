<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CheckIsLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $whiteList = [
            'api/login',
            'api/ding/getToken',
            'api/ding/userId',
            'api/ding/userNotify',
            'api/ding/getAuditedProjects',
            'api/ding/projectProgress',
            'api/ding/getAllProjects',
            'api/ding/projectProgressList',
            'api/ding/getAllWarning',
            'api/ding/projectInfo',
            'api/ding/projectScheduleInfo',
            'api/ding/actCompleteMoney',
            'api/ding/projectPlanInfo',
            'api/ding/uploadPic',
            'api/ding/auditProjectProgress',
            'api/ding/editProjectProgress',
            'ding/allActCompleteMoney'
        ];
        $uri = $request->route()->uri();
        if (!in_array($uri, $whiteList)) {
            if (!Auth::check()) {
                return response()->json(['result' => '登录失效，请重新登陆'], 401);
            }
        }
        return $next($request);
    }
}
