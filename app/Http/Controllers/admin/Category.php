<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Category extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("admin.category.index");
    }

    public function getCates(Request $request)
    {
        //获取数据
        $list = DB::table('lar_category')->orderByRaw('cat_sort ASC')->paginate(10)->toArray();

        //数据处理
        if(!empty($list['data'])){foreach($list['data'] as $key=>$val){
            $list['data'][$key]->cat_status = $val->cat_status==1?'显示':'隐藏';
        }}

        $retrun_arr = array('code'  => 0,'msg'   => '获取数据成功','count' => $list['total'],'data'  => $list['data']);
        return response()->json($retrun_arr);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("admin.category.add");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only(['cat_name','cat_sort','cat_status']);

        //查询是否存在
        $exist = DB::table('lar_category')->where("cat_name",$data['cat_name'])->first();
        if($exist){
            return response()->json(['status'=>'fail','msg'=>'分类名称已存在！']);
        }
        //保存到数据库
        $res = DB::table('lar_category')->insert($data);
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
        $info = DB::table('lar_category')->where("id",$id)->first();
        $return_data['info'] = $info;


        return view("admin.category.edit",$return_data);
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
        $data = $request->only(['cat_name','cat_sort','cat_status']);
        //查询是否存在
        $exist = DB::table('lar_category')->where("cat_name",$data['cat_name'])->where("id","!=",$id)->first();
        if($exist){
            return response()->json(['status'=>'fail','msg'=>'分类名称已存在！']);
        }
        //保存到数据库
        $res = DB::table('lar_category')->where("id",$id)->update($data);
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
        //判断该分类下是否有数据
        $num = DB::table('lar_course')->where("cat_id",$id)->count();
        if($num > 0){
            return response()->json(['status'=>'fail','msg'=>'删除失败：失败原因，该分类下还有课程！']);
        }

        $res = DB::table('lar_category')->where("id",$id)->delete();
        if($res){
            return response()->json(['status'=>'success','msg'=>'删除成功！']);
        }else{
            return response()->json(['status'=>'fail','msg'=>'删除失败！']);
        }
    }
}
