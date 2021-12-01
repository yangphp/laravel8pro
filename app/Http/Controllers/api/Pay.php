<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use AlipayTradeWapPayContentBuilder;
use AlipayTradeService;

class Pay extends Controller
{
    public function payH(Request $request){
		$total = $request->input('fee');
		$course_id = $request->input('course_id');
		$config = [
			'app_id' =>env('APP_ID'),
			'merchant_private_key' =>env('MER_PRI_KEY'),
			'notify_url' =>env('NOT_URL'),
			'return_url' =>'http://localhost:8080/#/pages/success/success?courseId='.$course_id,
			'charset' => "UTF-8",
			'sign_type'=>"RSA",
			'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
			'alipay_public_key' =>env('ALI_PUB_KEY')
		];	

		$out_trade_no = $request->input('order_no');

	    //订单名称，必填
    	$subject = DB::table('course')->where('id',$course_id)->value('course_title');

    	//付款金额，必填
    	$total_amount = $total;

    	//商品描述，可空
    	$body = $subject;

    	//超时时间
    	$timeout_express="1m";

    	$payRequestBuilder = new AlipayTradeWapPayContentBuilder();
    	$payRequestBuilder->setBody($body);
    	$payRequestBuilder->setSubject($subject);
    	$payRequestBuilder->setOutTradeNo($out_trade_no);
    	$payRequestBuilder->setTotalAmount($total_amount);
    	$payRequestBuilder->setTimeExpress($timeout_express);

    	$payResponse = new AlipayTradeService($config);
    	$result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
		return response()->json(['code'=>800,'data'=>$result,'msg'=>'获取成功']);
	}

	public function payA(Request $request){
		$total = $request->input('fee');
		$course_id = $request->input('course_id');
		$aop = new AopClient;
		$aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
		$aop->appId = env('APP_ID');
		$aop->rsaPrivateKey = env('MER_PRI_KEY');
		$aop->format = "json";
		$aop->charset = "UTF-8";
		$aop->signType = "RSA";
		$aop->alipayrsaPublicKey = env('ALI_PUB_KEY');
		//实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
		$out_trade_no = $request->input('order_no');
		$request = new AlipayTradeAppPayRequest();
		
		// 异步通知地址
		$notify_url = env('NOT_URL');
		// 订单标题
		$subject = DB::table('course')->where('id',$course_id)->value('course_title');
		// 订单详情
		$body = $subject; 
		// 订单号，示例代码使用时间值作为唯一的订单ID号
		
		//SDK已经封装掉了公共参数，这里只需要传入业务参数
		$bizcontent = "{\"body\":\"".$body."\","
		                . "\"subject\": \"".$subject."\","
		                . "\"out_trade_no\": \"".$out_trade_no."\","
		                . "\"timeout_express\": \"30m\","
		                . "\"total_amount\": \"".$total."\","
		                . "\"product_code\":\"QUICK_MSECURITY_PAY\""
		                . "}";
		$request->setNotifyUrl($notify_url);
		$request->setBizContent($bizcontent);
		//这里和普通的接口调用不同，使用的是sdkExecute
		$response = $aop->sdkExecute($request);
		return response()->json(['code'=>800,'data'=>$response,'msg'=>'获取成功']);
	}

	public function aliCallback(Request $request){
		
		file_put_contents('data.txt',json_encode($request->all()));	
		$data = $request->all();
		$config = [
            'app_id' =>env('APP_ID'),
            'merchant_private_key' =>env('MER_PRI_KEY'),
            'notify_url' =>env('NOT_URL'),
            'return_url' =>'http://localhost:8080/#/pages/success/success',
            'charset' => "UTF-8",
            'sign_type'=>"RSA",
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
            'alipay_public_key' =>env('ALI_PUB_KEY')
        ];

		$alipaySevice = new AlipayTradeService($config);
		$result = $alipaySevice->check($data);
		if($result){
			file_put_contents('notify.txt','success');
			$order = DB::table('course_order')->where('order_no',$data['out_trade_no'])->first();
			if($order->fee==$data['total_amount']){
				DB::table('coupon_user')->where('user_id',$order->user_id)->where('coupon_id',$order->coupon_id)->update(['is_use'=>1]);
				DB::table('course_order')->where('order_no',$data['out_trade_no'])->update(['is_pay'=>1]);
				return 'success';
			}			
			
		}
		
	}
}
