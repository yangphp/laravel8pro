<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserCheck
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
		$token = $request->input('token');
		
		$config = getTokenConfig();
		try{
			$token = $config->parser()->parse($token);
		}catch(\RuntimeException $e){
			return response()->json(array('code'=>806,'status'=>'fail','msg' =>'token invalid','data'=>[]));
		}
		
		$mobile = $token->claims()->get('mobile');
		
		$timezone 	= new \DatetimeZone('Asia/Shanghai');
		$now 		= new \Lcobucci\clock\SystemClock($timezone);
		$valJwt 	= new \Lcobucci\JWT\Validation\Constraint\ValidAt($now);
		$config->setValidationConstraints($valJwt);
		$contrains = $config->ValidationConstraints();
		
		try{
			$config->validator()->assert($token,...$contrains);
		}catch(\RuntimeException $e){
			
			$token = createToken($mobile);
			$data = $request->all();
			unset($data['sign']);
			unset($data['nonce']);
			unset($data['timestamp']);
			$data['token'] 	= $token;
			//获取当前的
			$path = $request->path();
			unset($data["/".$path]);
			
			return response()->json(array('code'=>807,'status'=>'fail','msg' =>'token expired ','data'=>$data));
		}
		
		
        return $next($request);
    }
}
