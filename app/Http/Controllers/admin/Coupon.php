<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Coupon extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("admin.coupon.index");
    }
	
	public function getCoupons(Request $request)
    {
        //获取数据
        $list = DB::table('lar_coupon')->orderByRaw('id ASC')->paginate(10)->toArray();
        //数据处理
        //if(!empty($list['data'])){foreach($list['data'] as $key=>$val){
        //    $list['data'][$key]->cat_status = $val->cat_status==1?'显示':'隐藏';
        //}}

        $retrun_arr = array('code'  => 0,'msg'   => '获取数据成功','count' => $list['total'],'data'  => $list['data']);
        return response()->json($retrun_arr);
    }
	
	public function status(Request $request)
    {
		$id 	= $request->input('id');
		$is_on  = $request->input('is_on');
        //获取数据
        $res = DB::table('lar_coupon')->where('id',$id)->update(['is_on'=>$is_on]);
       

        return response()->json(['status'=>'success','msg'=>'修改成功！']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("admin.coupon.add");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $data = $request->only(['coupon_name','coupon_fee','total_fee','from_to']);
	   $from_to = explode(" - ",$data['from_to']);
	   
	   $from_time = strtotime($from_to[0]);
	   $to_time = strtotime($from_to[1]);
	   
	   $data['from_time'] 	= $from_time;
	   $data['to_time'] 	= $to_time;
	   unset($data['from_to']);

	   //保存到数据库
        $res = DB::table('lar_coupon')->insert($data);
        if($res){
            return response()->json(['status'=>'success','msg'=>'添加成功！']);
        }else{
            return response()->json(['status'=>'fail','msg'=>'添加失败！']);
        }
	   
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $info = DB::table('lar_coupon')->where("id",$id)->first();
        
		
	   
	   $from_time = date("Y-m-d",$info->from_time);
	   $to_time = date("Y-m-d",$info->to_time);
	   $info->from_to = $from_time." - ".$to_time;
	   
		$return_data['info'] = $info;
		
		
         return view("admin.coupon.edit",$return_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->only(['coupon_name','coupon_fee','total_fee','from_to']);
	   $from_to = explode(" - ",$data['from_to']);
	   
	   $from_time = strtotime($from_to[0]);
	   $to_time = strtotime($from_to[1]);
	   
	   $data['from_time'] 	= $from_time;
	   $data['to_time'] 	= $to_time;
	   unset($data['from_to']);

	   //保存到数据库
		$res = DB::table('lar_coupon')->where("id",$id)->update($data);
		if($res){
			return response()->json(['status'=>'success','msg'=>'修改成功！']);
		}else{
			return response()->json(['status'=>'fail','msg'=>'修改失败！']);
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = DB::table('lar_coupon')->where("id",$id)->delete();
        if($res){
            return response()->json(['status'=>'success','msg'=>'删除成功！']);
        }else{
            return response()->json(['status'=>'fail','msg'=>'删除失败！']);
        }
    }
}
