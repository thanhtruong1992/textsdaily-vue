<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\IAuthenticationService;

class Domain{
    protected $authService;
    public function __construct(Request $request, IAuthenticationService $authService) {
        $this->authService = $authService;
    }
    public function handle($request, Closure $next) {
        $users = $this->authService->getAllUserGroup2();
        $domains = collect($users)->pluck("host_name")->all();
        $appUrl = str_replace('www.', '', parse_url(env("APP_URL"), PHP_URL_HOST));
        array_push($domains, $appUrl);
        $domains[] = 'textsdaily.success-ss.com.vn';
        $url = str_replace('www.', '', parse_url(url('/'), PHP_URL_HOST));
        if(!!in_array($url, $domains)) {
            return $next ( $request );
        }

        return redirect("403");
    }
}
