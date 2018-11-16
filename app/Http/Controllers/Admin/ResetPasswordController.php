<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Auth\IAuthenticationService;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class ResetPasswordController extends Controller {
	/*
	 * |--------------------------------------------------------------------------
	 * | Password Reset Controller
	 * |--------------------------------------------------------------------------
	 * |
	 * | This controller is responsible for handling password reset requests
	 * | and uses a simple trait to include this behavior. You're free to
	 * | explore this trait and override any methods you wish to tweak.
	 * |
	 */

	//use ResetsPasswords;

	/**
	 * Where to redirect users after resetting their password.
	 *
	 * @var string
	 */
	//protected $redirectTo = '/home';

    protected $request;
    protected $authService;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(Request $request, IAuthenticationService $authService) {
		//$this->middleware ( 'guest' );
		$this->request = $request;
		$this->authService = $authService;
	}

	public function viewResetPassowrd() {
        $result = $this->authService->checkTokenForgotPassword($this->request);
        if(!$result->status) {
            $error = (object)$result->error;
            Session::flash ( 'error', $error->message );
        }

        return view('reset-password', ["token_reset" => $this->request->get("token_reset")]);
	}

	public function resetPassword() {
        $result = $this->authService->resetPassword($this->request);

        if(!$result->status) {
            $error = (object)$result->error;
            Session::flash ( 'error', $error->message );
            return redirect ()->back ();
        }

        Session::flash ( 'success', Lang::get('notify.change_password_success') );
        return redirect ('login');
	}
}
