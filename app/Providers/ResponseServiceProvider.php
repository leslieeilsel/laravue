<?php


namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 自定义Json响应格式
        Response::macro('formatJson', function ($data = [], $message = 'success', $code = 200) {
            return new JsonResponse([
                // 'code' => $code,
                'data'    => $data,
                'message' => $message,
            ], 200);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
