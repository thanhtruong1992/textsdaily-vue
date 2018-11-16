<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Language {
	public function __construct(Request $request) {
	}
	public function handle($request, Closure $next) {
		// check session locale
		if (! $request->session ()->has ( 'locale' )) {

			// get language of request
			/* $languages = $request->server ( 'HTTP_ACCEPT_LANGUAGE' );
			$arr = explode ( ',', $languages );
			$language = count ( $arr ) > 0 ? $arr [0] : 'en'; */
			$language = 'en';

			// set language app
			App::setLocale ( $language );

			// put language into session
			$request->session ()->put ( 'locale', $language );
		}

		return $next ( $request );
	}
}
