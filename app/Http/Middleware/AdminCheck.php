<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $admin_name = session('admin_name');

        if($admin_name){
            View::share('admin_name',$admin_name);
        }else{
            return redirect("/admin/login");
        }

        return $next($request);
    }
}
