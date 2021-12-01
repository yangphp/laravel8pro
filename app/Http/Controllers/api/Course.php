<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Course extends Controller
{
	//获取首页课程列表
    public function getIndexCourse()
    {
        $courses = DB::table("lar_course")->where("is_on",1)->orderByRaw("id DESC")
                    ->get(['id','course_title','course_img','rate','study_p','ori_price','pro_price'])->toArray();
        if($courses){
            $return_data = array('code'=>800,'msg' =>'请求成功','data'=>$courses);
        }else{
            $return_data = array('code'=>804,'msg' =>'暂无数据','data'=>'');
        }
        return response()->json($return_data);
    }
	//获取课程详情
    public function getCourseDetail(Request $request)
    {
        $courseId = $request->input('courseId');

        $info = DB::table("lar_course")->where("id",$courseId)->first();
        $intro = preg_replace('/(<img.+?src=")(.+?>)/',"$1".env('API_URL')."$2",$info->intro);
        $info->intro = $intro;
		
		//获取第一个视频
		$video_url = DB::table("lar_catlog")->where("course_id",$courseId)->where("pid","<>",0)->value('video_url');
		$info->video_url = env('OSS_HOST')."/".$video_url;
		//获取优惠券
		$now_time = time();
		$coupon = DB::table('lar_coupon')->where("from_time",'<',$now_time)->where("to_time",'>',$now_time)->where("is_on",1)->first(['id','coupon_name','coupon_fee']);
		if(!empty($coupon)){
			$info->coupon_id = $coupon->id;
			$info->coupon_name = $coupon->coupon_name;
			$info->coupon_fee = $coupon->coupon_fee;
		}
		
        if($info){
            $return_data = array('code'=>800,'msg' =>'请求成功','data'=>$info);
        }else{
            $return_data = array('code'=>804,'msg' =>'暂无数据','data'=>'');
        }
        return response()->json($return_data);
    }
	//获取推荐的课程
    public function getTuiCourse(Request $request)
    {
        $courses = DB::table("lar_course")->where("is_on",1)->where("is_special",1)->orderByRaw("id DESC")
                    ->get(['id','course_title','course_img','rate','study_p','ori_price','pro_price'])->toArray();
        if($courses){
            $return_data = array('code'=>800,'msg' =>'请求成功','data'=>$courses);
        }else{
            $return_data = array('code'=>804,'msg' =>'暂无数据','data'=>'');
        }
        return response()->json($return_data);
    }
	//获取某个分类下的课程
    public function getCatCourse(Request $request)
    {
        $cat_id = $request->input('cat_id');

        $courses = DB::table("lar_course")->where("is_on",1)->where("cat_id",$cat_id)->orderByRaw("id DESC")
                    ->get(['id','course_title','course_img','rate','study_p','ori_price','pro_price'])->toArray();

        if($courses){
            $return_data = array('code'=>800,'msg' =>'请求成功','data'=>$courses);
        }else{
            $return_data = array('code'=>800,'msg' =>'暂无数据','data'=>'');
        }
        return response()->json($return_data);
    }
	
	//获取课程目录
	public function getCatlog(Request $request)
    {
        $courseId = $request->input('courseId');
		//获取数据
        $catlogs = DB::table("lar_catlog")->where("course_id",$courseId)->get()->toArray();
		$length  = count($catlogs);
		//将catlogs转换为纯数组
		$catlogs = json_decode(json_encode($catlogs),true);
		//将catlogs转换为二位数组
		$data = $this->dealCatlogs($catlogs);
		
		$data['length'] = $length;
		$data['oss_host'] = env('OSS_HOST');
		
		$rearr['catlogs'] = $data;
		$rearr['length'] = $length;
		$rearr['oss_host'] = env('OSS_HOST')."/";

        if($data){
            $return_data = array('code'=>800,'msg' =>'请求成功','data'=>$rearr);
        }else{
            $return_data = array('code'=>800,'msg' =>'暂无数据','data'=>'');
        }
        return response()->json($return_data);
    }
	
	//处理课程目录
	public function dealCatlogs($data)
	{
		foreach($data as $key=>$val)
		{
			if($val['pid'] == 0)
			{
				$catlogs[$key]['title'] = $val['catlog_title'];
				$catlogs[$key]['id'] = $val['id'];
			}
		}
		
		foreach($catlogs as $key=>$val)
		{
			foreach($data as $k2=>$v2)
			{
				if($v2['pid'] == $val['id'])
				{
					$catlogs[$key]['catlog'][] = $v2;
				}
			}
		}
		
		return $catlogs;
	}
	
	//获取课程url 
	public function getVideoUrl(Request $request)
	{
		$catlog_id = $request->input('id');
		
		//用户是否购买课程
		
		//课程是否免费
		$video = DB::table("lar_catlog")->where("id",$catlog_id)->first();
		$video_url = $video->video_url;
		return response()->json(array('code'=>800,'msg' =>'请求成功','data'=>['video_url'=>$video_url]));
	}
	
	//获取支付的课程
	public function getPayCourse(Request $request)
	{
		$courseId = $request->input('courseId');
		$token = $request->input('token');
		
		//获取课程信息
		$course = DB::table("lar_course")->where("id",$courseId)->first(['id','course_title','course_img','pro_price']);
		
		
		//获取用户手机号
		$user_mobile = getMobile($token);
		//获取uid 
		$user_id = DB::table("lar_users")->where("user_mobile",$user_mobile)->value('user_id');
		
		//获取优惠券id
		$coupon_id = DB::table("lar_coupon_user")->where("user_id",$user_id)->where("is_use",0)->value('coupon_id');
		$course->actual_fee = $course->pro_price;
		
		$order['user_id'] = $user_id;
		$order['couse_id'] = $courseId;
		$order['order_no']  = time().mt_rand(10000,99999);
		$order['fee']		= $course->actual_fee;
		$order['coupon_id'] = 0;
		
		
		
		if(!$coupon_id){
			$this->saveOrder($order);
			
			$course->order_no 	= $order['order_no'];
			$course->coupon_id 	= $order['coupon_id'];
		
			return response()->json(array('code'=>800,'msg' =>'请求成功','data'=>['course'=>$course]));
		}
		
		//获取优惠券
		$coupon = DB::table("lar_coupon")->where("id",$coupon_id)->where("from_time",'<',time())->where("to_time",'>',time())->first();
		if(!$coupon || $course->pro_price < $coupon->total_fee)
		{
			$this->saveOrder($order);
			
			$course->order_no 	= $order['order_no'];
			$course->coupon_id 	= $order['coupon_id'];
			
			return response()->json(array('code'=>800,'msg' =>'请求成功','data'=>['course'=>$course]));
		}
		
		$course->actual_fee = $course->pro_price - $coupon->coupon_fee;
		$course->coupon_fee = $coupon->coupon_fee;
		
		$order['fee']		= $course->actual_fee;
		$this->saveOrder($order);
		
		$course->order_no 	= $order['order_no'];
		$course->coupon_id 	= $coupon_id;
		
		return response()->json(array('code'=>800,'msg' =>'请求成功','data'=>['course'=>$course]));
		
	}
	
	//保存订单
	public function saveOrder($data){
		$data['create_time'] = time();
		DB::table('lar_course_order')->insert($data);
		
	}
	
}
