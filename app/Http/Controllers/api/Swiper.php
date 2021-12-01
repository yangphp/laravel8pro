<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class Swiper extends Controller
{
    public function getSwiper()
    {
        $swipers = DB::table("lar_swiper")->where("swiper_status",1)->orderByRaw("swiper_sort ASC")->get()->toArray();
        if($swipers){
            $return_data = array('code'=>800,'msg' =>'请求成功','data'=>$swipers);
        }else{
            $return_data = array('code'=>804,'msg' =>'暂无数据','data'=>'');
        }
        return response()->json($return_data);
    }
}
