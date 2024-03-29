<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckWaiter
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
        }elseif (Auth::user()->role != 'waiter'){
            return redirect('/home');
        }elseif(checkStatus(Auth::user()) == false){
            auth()->logout();
            return redirect('/login');
        }
        return $next($request);
    }
}
