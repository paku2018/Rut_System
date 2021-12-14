<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMember
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
        if(Auth::guard()->check() === false){
            return redirect('/login');
        }elseif (Auth::user()->role != 'waiter' && Auth::user()->role == 'cashier'){
            return redirect('/home');
        }
        return $next($request);
    }
}
