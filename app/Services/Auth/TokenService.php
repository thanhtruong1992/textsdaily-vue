<?php

namespace App\Services\Auth;

use App\Services\BaseService;
use App\Repositories\Auth\ITokenRepository;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class TokenService extends BaseService implements ITokenService {
    protected $tokenRepo;

    public function __construct(ITokenRepository $tokenRepo)
    {
        $this->tokenRepo = $tokenRepo;
    }

    /**
     * fn create token
     */
    public function createToken() {
        try {
            $user = Auth::user();
            // token
            $token = md5($user->id . time() . uniqid());
            $params = [
                'user_id' => $user->id,
                'token' => $token,
                'expired_at' => Carbon::now()->addYear(config('constants.expired_token'))
            ];
            $result = null;

            //find token
            $tokenUser = $this->tokenRepo->findByField('user_id', $user->id)->first();
            if(!empty($tokenUser)) {
                // update token
                $result = $this->tokenRepo->update($params, $tokenUser->id);
            }else {
                // create token
                $result = $this->tokenRepo->create($params);
            }

            return $this->success($result);
        }catch(\Exception $e) {
            return $this->fail();
        }
    }

    /**
     * fn get token by user id
     */
    public function getTokenByUserId() {
        try{
            $user = Auth::user()->id;
            return $this->tokenRepo->findByField('user_id',$user)->first();
        } catch(\Exception $e) {
            return $this->fail();
        }
    }

    /**
     * fn get token by token 
     */
    public function getTokenByToken($token) {
        try{
            return $this->tokenRepo->findByField('token',$token)->first();
        } catch(\Exception $e) {
            return $this->fail();
        }
    }

    /**
     * fn update exprired time 
     */
    public function updateExpriredTime($attributes, $id) {
        try{
            return $this->tokenRepo->update($attributes, $id);
        } catch(\Exception $e) {
            return $this->fail();
        }
    }

    
}