<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Category extends Controller
{
    public function getCats()
    {
        $cates = DB::table("lar_category")->where("cat_status",1)->orderByRaw("cat_sort ASC")->get()->toArray();
        if($cates){
            $return_data = array('code'=>800,'msg' =>'请求成功','data'=>$cates);
        }else{
            $return_data = array('code'=>804,'msg' =>'暂无数据','data'=>'');
        }
        return response()->json($return_data);
    }
}
