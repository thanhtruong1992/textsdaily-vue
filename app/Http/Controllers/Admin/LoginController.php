<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\Auth\IAuthenticationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController {
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
            return $this->success(Auth::user());
        }

        if (count ( $result->error ) > 0) {
            return $this->badRequest(null, $result->error);
        }

        return $this->badRequest(Lang::get ( 'notify.login_error' ));
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

    public function getMe(){
        try {
            if(Auth::check()) {
                $user = Auth::user();
                $user->childCurrentUser = $this->authService->getAllUserChildrenByParent($user->id);
                return $this->success($user);
            }

            return $this->badRequest();
        }catch(\Exception $e) {
            return $this->badRequest($e->getMessage());
        }
    }

    public function logout() {
        Auth::logout();
        session()->forget('other_role');
        return redirect ( 'login' );
    }
}
