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
    public function handle(Request $request, Closure $next, $permission = "home")
    {
        if(Auth::guard()->check() === false){
            return redirect('/login');
        }
        $user = Auth::user();
        if ($user->role != 'restaurant' && $user->role != "member"){
            abort(404);
        }
        if(checkStatus($user) == false){
            auth()->logout();
            return redirect('/login');
        }
        if (checkPermission($user, $permission) == false) {
            auth()->logout();
            return redirect('/login');
        }

        return $next($request);
    }
}
