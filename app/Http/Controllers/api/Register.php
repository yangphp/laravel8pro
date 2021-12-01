<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Register extends Controller
{
    public function sendCode(Request $request)
    {
        $mobile = $request->input('mobile');
		
		$code = mt_rand(1000,9999);
		$res = sendMobileYzm($mobile,$code);
		$ress = json_decode($res,true);
		
		//保存验证码到数据库
		if($ress['status']=="OK")
		{
			DB::table('lar_code')->insert(['mobile'=>$mobile,'code'=>$code,'add_datetime'=>date("Y-m-d H:i:s")]);
		}
		$return_data = array('code'=>800,'msg' =>'请求成功','data'=>$ress);
		return response()->json($return_data);
    }
	
	//注册
	public function register(Request $request){
		$mobile = $request->input('mobile');
		$code = $request->input('code');
		
		//判断验证码是否正确
		$code_info = DB::table('lar_code')->where("mobile",$mobile)->orderByRaw("id DESC")->first();
		if($code_info->code!=$code)
		{
			$return_data = array('code'=>805,'status'=>'fail','msg' =>'验证码输入错误','data'=>[]);
		}else{
			$user = DB::table('lar_users')->where('user_mobile',$mobile)->first();
			if($user){
				$return_data = array('code'=>805,'status'=>'fail','msg' =>'此手机号已注册','data'=>[]);
			}else{
				//注册数据到用户表
				DB::table('lar_users')->insert(['user_mobile'=>$mobile,'add_datetime'=>date("Y-m-d H:i:s")]);
				$token = createToken($mobile);
				$return_data = array('code'=>800,'status'=>'success','msg' =>'注册成功','data'=>['token'=>$token]);
			}
		}
		return response()->json($return_data);
	}
	
	//登录
	public function login(Request $request){
		$mobile = $request->input('mobile');
		$code = $request->input('code');
		
		//判断验证码是否正确
		$code_info = DB::table('lar_code')->where("mobile",$mobile)->orderByRaw("id DESC")->first();
		if($code_info->code!=$code)
		{
			$return_data = array('code'=>805,'status'=>'fail','msg' =>'验证码输入错误','data'=>[]);
		}else{
			$user = DB::table('lar_users')->where('user_mobile',$mobile)->first();
			if($user){
				$token = createToken($mobile);
				$return_data = array('code'=>800,'status'=>'success','msg' =>'登录成功','data'=>['token'=>$token]);
			}else{
				
				$return_data = array('code'=>805,'status'=>'fail','msg' =>'登录失败','data'=>[]);
			}
		}
		return response()->json($return_data);
	}
}
