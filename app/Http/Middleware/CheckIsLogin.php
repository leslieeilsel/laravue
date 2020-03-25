<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIsLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $whiteList = [
            'api/login',
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
