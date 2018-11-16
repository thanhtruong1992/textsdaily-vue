<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Session;
use App\Services\Auth\ITokenService;
use App\Services\Auth\IAuthenticationService;


class AuthenticationApi{
    
    public function __construct(Request $request, ITokenService $tokenService, IAuthenticationService $authenticationService) {
        $this->tokenService = $tokenService;
        $this->authenticationService = $authenticationService;
    }

    public function handle($request, Closure $next) {
        $authorization = $request->header('Authorization');
        $authorization = !strpos($authorization, "Bearer") ? trim(str_replace("Bearer", "",$authorization)) : "" ;

        // check token empty
        if(strlen($authorization) > 0) {
            $token = $this->tokenService->getTokenByToken($authorization);
            
            // valid token
            if( !empty($token) ) {
                if( Carbon::parse($token->expired_at)->gt(Carbon::now())   ) {
                    
                    $attributes['expired_at'] = Carbon::now()->addYear(config('constants.expired_token'));
                    // update exprired time   
                    $result = $this->tokenService->updateExpriredTime($attributes, $token->id);
                }

                $result = $this->authenticationService->loginOtherRoleWithId($token->user_id);
                return $next ( $request );
            }
        }

        return response()->json([ 
            "message" => trans('token.unauthorized_error')
        ], 401);    
    }
}
