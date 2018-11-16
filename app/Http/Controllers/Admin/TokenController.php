<?php

namespace App\Http\Controllers;

use App\Services\Auth\ITokenService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;

class TokenController extends Controller {
    protected $tokenService;

    public function __construct(ITokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * fn get token page
     */
    public function index() {
        $token = $this->tokenService->getTokenByUserId();
        if($token) {
            return view('admins.tokens.index',[
                "token" => $token->api_token
            ]);
        } else {
            return view('admins.tokens.index',[
                "token" => trans("token.no_token")
            ]);
        }   
    }

    /**
     * fn create token
     */
    public function create() {
        try {
            $result = $this->tokenService->createToken();

            if(!$result->status) {
                Session::flash ( 'error', Lang::get ( 'notify.token_create_error' ));
                return redirect ()->back ();
            }
            
            Session::flash ( 'success', Lang::get ( 'notify.token_create_success' ));
            return redirect ()->back ();
        }catch(\Exception $e) {
            Session::flash ( 'error', Lang::get ( 'notify.token_create_error' ));
            return redirect ()->back ();
        }
    }
   
}