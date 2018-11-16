<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Services\Auth\IAuthenticationService;
use App\Facades\CustomLog;

class RegisterController extends Controller {
	protected $authService;

	/**
	 * Where to redirect users after registration.
	 *
	 * @var string
	 */
	protected $redirectTo = '/home';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(IAuthenticationService $IAuthService) {
		$this->middleware ( 'guest' );
		$this->authService = $IAuthService;
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param array $data
	 * @return \App\Models\User
	 */
	protected function create(Request $request) {
		/* $allUser = $this->authService->getAllUser ();
		CustomLog::info ( 'User info', 'UserLog-' . date ( 'd-m-Y' ), $allUser ); */

		$result = $this->authService->register ( $request );

		if (! ! $result->status) {
			Session::flash ( 'success', Lang::get ( 'notify.register_user' ) );
			return redirect ( '/login' );
		}

		if (count ( $result->error ) > 0) {
			$message = ( object ) $result->error->toArray ();
			$error = count ( $message->email ) > 0 ? $message->email [0] : '';
			if (isset ( $error )) {
				Session::flash ( 'error', $error );
			}
		}
		return redirect ()->back ();
	}

	public function uploadCSV(Request $request) {
	    $result = $this->authService->uploadCSV($request);
	}
}
