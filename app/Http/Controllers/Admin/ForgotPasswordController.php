<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Auth\IAuthenticationService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;

class ForgotPasswordController extends Controller {
	/*
	 * |--------------------------------------------------------------------------
	 * | Password Reset Controller
	 * |--------------------------------------------------------------------------
	 * |
	 * | This controller is responsible for handling password reset emails and
	 * | includes a trait which assists in sending these notifications from
	 * | your application to your users. Feel free to explore this trait.
	 * |
	 */

	//use SendsPasswordResetEmails;

    protected $request;
    protected $authService;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(Request $request, IAuthenticationService $authService) {
        $this->request = $request;
        $this->authService = $authService;
	}

	public function forgotPassword() {
        $result = $this->authService->forgotPassword($this->request);

        if (!!$result->status) {
            Session::flash ( 'success', Lang::get ( 'notify.forgot_password_success' ) );
            return redirect ( 'login' );
        }

        if (count ( $result->error ) > 0) {
            $message = ( object ) $result->error;
            if (isset ( $message)) {
                Session::flash ( 'error', $message);
            }
        }else {
            Session::flash ( 'error', Lang::get('notify.user_does_not_exist'));
        }

        return redirect ()->back ();
	}
}
