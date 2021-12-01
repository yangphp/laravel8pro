<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Index extends Controller
{
    public function index()
    {
        return view('admin.index.index');
    }

    public function welcome()
    {
        return view('admin.index.welcome');
    }
    public function logout()
    {
        session(['admin_name'=>null,'admin_id'=>null]);
        return  response()->json(['status'=>'success','msg'=>'退出成功']);
    }
}
