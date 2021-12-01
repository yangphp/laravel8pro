<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Swiper extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //加载模板
        return view("admin.swiper.index");
    }

    public function getSwiper(Request $request)
    {
        //获取数据
        $list = DB::table('lar_swiper')->orderByRaw('swiper_sort ASC')->paginate(2)->toArray();

        //数据处理
        if(!empty($list['data'])){foreach($list['data'] as $key=>$val){
            $list['data'][$key]->swiper_status = $val->swiper_status==1?'显示':'隐藏';
        }}

        $retrun_arr = array(
        'code'  => 0,
        'msg'   => '获取数据成功',
        'count' => $list['total'],
        'data'  => $list['data']
        );
        return response()->json($retrun_arr);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("admin.swiper.add");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only(['title','jumpurl','swiper_sort','swiper_status','imgurl']);
        $data['add_datetime'] = date("Y-m-d H:i:s");

        //保存到数据库
        $res = DB::table('lar_swiper')->insert($data);
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
       $info = DB::table('lar_swiper')->where("id",$id)->first();
       $return_data['info'] = $info;


       return view("admin.swiper.edit",$return_data);
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
        if(empty($id)) return response()->json(['status'=>'fail','msg'=>'修改失败！']);

        $data = $request->only(['title','jumpurl','swiper_sort','swiper_status','imgurl']);
        //保存到数据库
        $res = DB::table('lar_swiper')->where("id",$id)->update($data);
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
        //保存到数据库
        $res = DB::table('lar_swiper')->where("id",$id)->delete();
        if($res){
            return response()->json(['status'=>'success','msg'=>'删除成功！']);
        }else{
            return response()->json(['status'=>'fail','msg'=>'删除失败！']);
        }
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');

        $res = response()->json(uploadImg($file,"uploads/swiper"));
        return $res;
    }
}
