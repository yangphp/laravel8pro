<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("sendCode","api\Register@sendCode");
Route::post("register","api\Register@register");
Route::post("login","api\Register@login");

Route::get("getSwiper","api\Swiper@getSwiper");
Route::get("getCats","api\Category@getCats");
Route::get("getIndexCourse","api\Course@getIndexCourse");
Route::get("getCourseDetail","api\Course@getCourseDetail");
Route::get("getTuiCourse","api\Course@getTuiCourse");
Route::get("getCatCourse","api\Course@getCatCourse");
Route::get("getCourseCatlog","api\Course@getCatlog");


Route::group(['middleware'=>'user_check'],function(){
	
	Route::post("getVideoUrl","api\Course@getVideoUrl");
	Route::post("getPayCourse","api\Course@getPayCourse");
	Route::post("payH","api\Pay@payH");
	
	
	Route::post("storeCoupon","api\Coupon@storeCoupon");
	
});


