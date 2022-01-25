<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckResAdmin
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
        if(Auth::guard()->check() === false || Auth::user()->role == 'waiter' || Auth::user()->role == 'cashier'){
            return redirect('/home');
        }
        if(checkStatus(Auth::user()) == false){
            auth()->logout();
            return redirect('/login');
        }
        return $next($request);
    }
}
