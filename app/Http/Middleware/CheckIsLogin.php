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
            'api/index/login',
            'api/index/dictData',
            'api/index/regist',
            'api/index/registUpdate',
            'api/index/application',
            'api/index/applicationList',
            'api/index/applicationUpdate',
            'api/index/searchTitle',
            'api/index/userInfo',
            'api/index/updatePwdMail',
            'api/index/updatePwd'
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
