<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Session;

class AuthenticationAdmin{
    public function __construct(Request $request) {
    }
    public function handle($request, Closure $next) {
        if (Auth::check()) {
            Session::put('startAuth', Carbon::now()->addMinute(1)->toDateTimeString());
            return $next ( $request );
        }
        
        return response()->json([
            'message' => 'Unauthorized',
            'status' => false
        ], 401);
    }
}
