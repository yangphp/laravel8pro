<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ApiCheck
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
        $api_key = env('API_KEY');

        $data    = $request->all();
        //签名验证
        if(!isset($data['sign'])){
             return response()->json(['code'=>803,'msg'=>'非法的请求参数']);
        }
        $sign    = $data['sign'];
        unset($data['sign']);
        $data['key'] = $api_key;
        ksort($data);

        $sign_str = http_build_query($data);
        $new_sign = md5($sign_str);
        if($new_sign != $sign)
        {
            return response()->json(['code'=>801,'msg'=>'非法的接口请求']);
        }
        if(Redis::get($sign))
        {
            return response()->json(['code'=>802,'msg'=>'请勿重复请求接口']);
        }
        //将sign存入redis
        Redis::setex($sign,60,1);



        return $next($request);
    }
}
