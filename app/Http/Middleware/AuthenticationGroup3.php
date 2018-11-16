<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticationGroup3 {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (Auth::check () && Auth::user ()->isGroup3 ()) {
            return $next ( $request );
        }
        
        return redirect ( 'login' );
    }
}
