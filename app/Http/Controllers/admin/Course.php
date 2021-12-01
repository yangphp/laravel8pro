<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Course extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("admin.course.index");
    }

    public function getCourse()
    {
        //获取数据
        $list = DB::table('lar_course')->orderByRaw('id DESC')->paginate(10,['id','course_title','cat_id','course_img','rate','study_p','ori_price','pro_price','is_special','is_on','add_time'])->toArray();

        //数据处理
        if(!empty($list['data'])){foreach($list['data'] as $key=>$val){

            $cat_name = DB::table('lar_category')->where("id",$val->cat_id)->value('cat_name');
            $list['data'][$key]->cat_name = $cat_name;
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
        $cat_list = DB::table('lar_category')->where('cat_status',1)
                    ->orderByRaw("cat_sort ASC")->get(['id','cat_name'])->toArray();

        return view("admin.course.add",['cat_list'=>$cat_list]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only(['course_title','cat_id','course_img','ori_price','pro_price','intro','is_special','is_on']);
        $data['add_time'] = time();
        $data['study_p']  = mt_rand(100,300);

        //保存到数据库
        $res = DB::table('lar_course')->insert($data);
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
        $info = DB::table('lar_course')->where("id",$id)->first();
        $return_data['info'] = $info;

        $cat_list = DB::table('lar_category')->where('cat_status',1)
                    ->orderByRaw("cat_sort ASC")->get(['id','cat_name'])->toArray();
        $return_data['cats'] = $cat_list;

         return view("admin.course.edit",$return_data);
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
         //更新数据
        $data = $request->only(['course_title','cat_id','course_img','ori_price','pro_price','intro','is_special','is_on']);

        //保存到数据库
        $res = DB::table('lar_course')->where("id",$id)->update($data);
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
        $res = DB::table('lar_course')->where("id",$id)->delete();
        if($res){
            return response()->json(['status'=>'success','msg'=>'删除成功！']);
        }else{
            return response()->json(['status'=>'fail','msg'=>'删除失败！']);
        }
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');

        $res = uploadImg($file,"uploads/course");
        return response()->json($res);
    }
    //上传课程详情图片
    public function upIntroImg(Request $request)
    {
        $file = $request->file('file');

        $res = uploadImg($file,"uploads/course_intro");

        if($res['status']=='success'){

            $arr = array(
                'errno' =>0,
                'data'  =>array(
                    array('url'=>$res['url'],'alt'=>'')
                )
            );
            return response()->json($arr);
        }else{

            $arr = array(
                'errno' =>1,
                'data'  =>[]
            );
            return response()->json($arr);
        }
    }
    //课程目录
    public function catlog($id)
    {
		//获取课程目录
		$chapter = DB::table('lar_catlog')->where('course_id',$id)->where("pid",0)->orderByRaw(" id ASC ")->get()->toArray();
		
		
       return view("admin.course.catlog",['course_id'=>$id,'chapter'=>$chapter]);
    }
	//保存课程目录
	public function saveChapter(Request $request){
		$data = $request->only(['course_id','catlog_title']);

        //保存到数据库
        $res = DB::table('lar_catlog')->insert($data);
        if($res){
            return response()->json(['status'=>'success','msg'=>'添加成功！']);
        }else{
            return response()->json(['status'=>'fail','msg'=>'添加失败！']);
        }
	}
	//保存课程
	public function saveVideo(Request $request){
		
		$data = $request->only(['course_id','catlog_title','pid','video_url','is_free']);
        //保存到数据库
        $res = DB::table('lar_catlog')->insert($data);
        if($res){
            return response()->json(['status'=>'success','msg'=>'添加成功！']);
        }else{
            return response()->json(['status'=>'fail','msg'=>'添加失败！']);
        }
	}
}
