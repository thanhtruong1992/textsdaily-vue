<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\Auth\IAuthenticationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;
use Auth;
use Carbon\Carbon;

class LoginController extends Controller {
    /*
     * |--------------------------------------------------------------------------
     * | Login Controller
     * |--------------------------------------------------------------------------
     * |
     * | This controller handles authenticating users for the application and
     * | redirecting them to your home screen. The controller uses a trait
     * | to conveniently provide its functionality to your applications.
     * |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        //$this->middleware ( 'guest' )->except ( 'logout' );
        $this->authService = $IAuthService;
    }
    public function viewLogin() {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isGroup4()) {
                return redirect ( 'reader/dashboard' );
            }
            if ($user->isGroup3()) {
                return redirect ( 'admin/dashboard' );
            }

            if ($user->isGroup2()) {
                return redirect( 'client/dashboard' );
            }

            if ($user->isGroup1()) {
                return redirect( 'agency/dashboard' );
            }
        }

        return view ( 'login' );
    }
    public function login(Request $request) {
        $result = $this->authService->loginUser( $request );
        if (!!$result->status) {
            Session::flash ( 'success', Lang::get ( 'notify.login_user' ) );
            return redirect ( 'admin/dashboard' );
        }

        if (count ( $result->error ) > 0) {
            $message = ( object ) $result->error->toArray ();
            if (isset ( $message)) {
                Session::flash ( 'error', $message);
            }
        }else {
            Session::flash ( 'error', Lang::get ( 'notify.login_error' ));
        }
        return redirect ()->back ();
    }

    public function loginWithOtherRole(Request $request, $id) {
        if (Auth::user()->isGroup1() || Auth::user()->isGroup2()) {
            if (session()->has('other_role')) {
                $value = session('other_role', []);
                $value[] = Auth::user()->name;
                session(['other_role' => $value]);
            } else {
                $value = [Auth::user()->name];
                session(['other_role' => $value]);
            }
            $result = $this->authService->loginOtherRoleWithId($id);
        }
        return redirect ()->back ();
    }

    public function loginWithParentRole(Request $request) {
        if (session()->has('other_role')) {
            $value = session('other_role', []);
            array_pop($value);
            session(['other_role' => $value]);
            $result = $this->authService->loginOtherRoleWithId(Auth::user()->parent_id);
        }
        return redirect ()->back ();
    }

    public function logout() {
        Auth::logout();
        session()->forget('other_role');
        return redirect ( 'login' );
    }
}
