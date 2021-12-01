<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Login extends Controller
{
    public function index(Request $request)
    {
        if($request->isMethod('post')){

            //验证码验证
            if(!captcha_check($request->input('captcha')))
            {
                return response()->json(['status'=>'fail','msg'=>'验证码输入错误']);
            }
            //登录验证
            $admin = $request->only(['username','password']);
            $res = DB::table('lar_admins')->where('admin_name',$admin['username'])->first();
            if(!$res){
                return response()->json(['status'=>'fail','msg'=>'用户名不存在']);
            }
            if($res->admin_pwd!=md5($admin['password'])){
                return response()->json(['status'=>'fail','msg'=>'密码错误']);
            }else{
                //存入session
                session(['admin_id'=>$res->admin_id,'admin_name'=>$res->admin_name]);

                return response()->json(['status'=>'success','msg'=>'登录成功']);
            }


        }else{
            return view('admin.login.index');
        }

    }
}
