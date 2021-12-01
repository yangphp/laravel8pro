<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Coupon extends Controller
{
    //领取优惠券
	public function storeCoupon(Request $request)
	{
		$token 		= $request->input('token');
		$coupon_id 	= $request->input('coupon_id');
		
		$mobile = getMobile($token);
		//获取用户ID
		$user_id = DB::table('lar_users')->where("user_mobile",$mobile)->value('user_id');
		
		if(!empty($user_id)){
			//插入数据表
			$exist = DB::table('lar_coupon_user')->where("coupon_id",$coupon_id)->where("user_id",$user_id)->where("is_use",0)->first();
			if($exist)
			{
				 return response()->json(array('code'=>808,'msg' =>'你已经领取过了','data'=>''));
			}
			
			$res = DB::table('lar_coupon_user')->insert(['coupon_id'=>$coupon_id,'user_id'=>$user_id,'add_time'=>time()]);
			if($res){
				$return_data = array('code'=>800,'msg' =>'领取成功','data'=>'');
			}else{
				$return_data = array('code'=>808,'msg' =>'领取失败','data'=>'');
			}
			
		}else{
			$return_data = array('code'=>808,'msg' =>'领取失败','data'=>'');
		}
		
		 return response()->json($return_data);
		
		
	}
}
