<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::match(['get','post'],'/admin/login', "admin\Login@index");

Route::group(['middleware'=>'admin_check'],function(){
    Route::get('/admin/index', "admin\Index@index");
    Route::get('/admin/welcome', "admin\Index@welcome");
    Route::get('/admin/logout', "admin\Index@logout");

    //幻灯片
    Route::get('/admin/swiper/get_swipers',"admin\Swiper@getSwiper");
    Route::post('/admin/swiper/upload',"admin\Swiper@upload");
    Route::resource('/admin/swiper',"admin\Swiper");
    //课程分类
    Route::get('/admin/category/get_cates',"admin\Category@getCates");
    Route::resource('/admin/category',"admin\Category");

    //课程管理
    Route::get('/admin/course/get_course',"admin\Course@getCourse");
    Route::post('/admin/course/upload',"admin\Course@upload");
    Route::post('/admin/course/upIntroImg',"admin\Course@upIntroImg");
    Route::get('/admin/course/catlog/{id}',"admin\Course@catlog");
    Route::post('/admin/course/saveChapter',"admin\Course@saveChapter");
    Route::post('/admin/course/saveVideo',"admin\Course@saveVideo");
    Route::resource('/admin/course',"admin\Course");
    Route::get('/admin/get_sign','admin\Oss@getSign');
	Route::get('/admin/coupon/get_coupons',"admin\Coupon@getCoupons");
	Route::post('/admin/coupon/status',"admin\Coupon@status");
	Route::resource('/admin/coupon',"admin\Coupon");
	
	

});
Route::post('/admin/oss/callback','admin\Oss@callback');

