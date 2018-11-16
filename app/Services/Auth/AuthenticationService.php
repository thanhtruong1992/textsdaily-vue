<?php

namespace App\Services\Auth;

use App\Repositories\Auth\IAuthenticationRepository;
use App\Services\Auth\IAuthenticationService;
use App\Services\Auth\ValidationAuth;
use App\Services\BaseService;
use App\Entities\User;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\UploadService;
use App\Repositories\Auth\IForgotPasswordRepository;
use Illuminate\Support\Facades\Lang;
use Carbon\Carbon;
use App\Services\MailServices\MailService;
use App\Mail\ForgotPassword;

class AuthenticationService extends BaseService implements IAuthenticationService {
    protected $authRepo;
    protected $validation;
    protected $forgotPasswordRepo;
    protected $mailService;

    public function __construct(IAuthenticationRepository $authenticationRepo, ValidationAuth $validationAuth, IForgotPasswordRepository $forgotPasswordRepo, MailService $mailService) {
        $this->authRepo = $authenticationRepo;
        $this->validation = $validationAuth;
        $this->forgotPasswordRepo = $forgotPasswordRepo;
        $this->mailService = $mailService;
    }

    public function register($request) {
        $validator = $this->validation->registerUser ( $request );
        if (isset ( $validator )) {
            return $this->fail ( $validator );
        } else {
            $request['password'] = Hash::make($request['password']);
            $user = $this->authRepo->create ( $request->toArray () );
            return $this->success ( $user );
        }
    }
    public function getAllUser() {
        $users = $this->authRepo->all ();

        $results = array ();
        foreach ( $users->toArray () as $item ) {
            $results [$item ['id']] = new User ( $item );
        }
        return $results;
    }
    public function loginUser($request) {
        $validator = $this->validation->loginUser( $request );
        if (isset ( $validator )) {
            return $this->fail ( $validator );
        } else {
            if (Auth::attempt ( [
                    'username' => $request->username,
                    'password' => $request->password,
                    'blocked' => 0,
                    'status' => 'ENABLED'
            ] )) {
                // Check blocked Parent User
                $parentUser = $this->authRepo->findViaDB(Auth::user()->parent_id);
                if (isset($parentUser) && $parentUser->status == 'DISABLED' ) {
                    Auth::logout();
                    return $this->fail();
                }
                return $this->success ( [] );
            }
            return $this->fail();
        }
    }

    public function loginOtherRoleWithId($clientId) {
        $other = Auth::loginUsingId($clientId, false);
        return $other;
    }

    public function getUserInfo( $idUser ) {
        return $this->authRepo->with(['parent'])->find( $idUser );
    }

    public function getSenderList($userId) {
        $user = $this->authRepo->find($userId);
        $senderObject = $user->sender;
        $data = json_decode($senderObject);
        if ($data == null) {
            $data = [];
        }
        return $data;
    }

    public function addNewSender($userId, $sender) {
        $user = $this->authRepo->find($userId);
        $senderObject = $user->sender;
        $data = json_decode($senderObject);
        if ($data != null) {
            // Sender List have item
            if (isset($data->$sender)) {
                // Sender name exist
                return ["status" => false,
                        "sender_name" => $sender
                ];
            }

            // Added new element
            $data->$sender = $sender;

        } else {
            // Create new element
            $data = array();
            $data[$sender] = $sender;
        }

        // encode json
        $json_data = json_encode($data);
        $result = $this->authRepo->addNewSenderList($userId, $json_data);
        return ["status" => $result ? true : false,
                "sender_name" => $sender
        ];
    }

    /**
     * fn get all user group 3
     * @return array
     */
    public function getAllUserGroup3($userID = null, $toArray = true) {
        if($userID != "") {
            $users = $this->authRepo->findWhere([
                    "type" => "GROUP3",
                    "parent_id" => $userID
            ]);
        }else {
            $users = $this->authRepo->findByField("type", "GROUP3");
        }

        if ( $toArray ) {
            return $users->toArray();
        } else {
            return $users;
        }
    }

    /**
     * fn get all user group 2
     * @return array
     */
    public function getAllUserGroup2($userID = null, $toArray = true) {
        if($userID != "") {
            $users = $this->authRepo->findWhere([
                    "type" => "GROUP2",
                    "parent_id" => $userID
            ]);
        }else {
            $users = $this->authRepo->findByField("type", "GROUP2");
        }

        if ( $toArray ) {
            return $users->toArray();
        } else {
            return $users;
        }
    }

    /**
     * fn get all user monthly
     * @return array
     */
    public function getAllMonthlyUserAvailable() {
        return $this->authRepo->findWhere([
                'billing_type' => 'MONTHLY',
                'status'    => 'ENABLED'
        ]);
    }



    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Auth\IAuthenticationService::getChildrens()
     */
    public function getChildrens($userID) {
        return $this->authRepo->findWhere([
                'parent_id' => $userID,
                'status'    => 'ENABLED'
        ]);
    }

    /**
     * fn get all user childrend by Id
     * @return array
     */
    public function getAllUserChildrenByParent($userID = null, $toArray = true) {
        if($userID != "") {
            $users = $this->authRepo->findWhere([
                    "parent_id" => $userID
            ]);
        }else {
            $users = [];
        }

        if ( $toArray ) {
            return $users->toArray();
        } else {
            return $users;
        }
    }

    /**
     * FN forgot password
     * @param unknown $request
     * @return object
     */
    public function forgotPassword ($request) {
        $validator = $this->validation->forgotPassword( $request );

        // check validation
        if (!empty( $validator )) {
            return $this->fail ( $validator->toArray() );
        } else {
            // forgot password
            $user = $this->authRepo->findByField('username', $request->get("username", ''))->first();
            if(empty($user)) {
                return $this->fail();
            }

            $token = md5(time() . $request->get('username') . uniqid());
            $forgotPassword = $this->forgotPasswordRepo->create([
                    'user_id' => $user->id,
                    'token' => $token,
                    'expired_at' => Carbon::now()->addHours(2)
            ]);

            try {
                // send email
                $title = 'Forgot Password';
                $url = url('/reset-password?token_reset=' . $token);
                $objectContent = ( object ) array (
                        "url" => $url
                );
                $templateEmailObj = new ForgotPassword( $title, $objectContent);
                $this->mailService->notifyMail ( $user->email, $templateEmailObj );
            }catch(\Exception $e) {
                return $this->fail();
            }

            return $this->success($forgotPassword);
        }
    }

    /**
     * fn check token expired of forgot password
     * @param unknown $request
     * @return object|StdClass
     */
    public function checkTokenForgotPassword ($request) {
        $token = $request->get('token_reset', '');

        $forgotPassowrd = $this->forgotPasswordRepo->findByField('token', $token)->first();
        if(empty($forgotPassowrd)) {
            return $this->fail([
                    "message" => Lang::get("notify.token_forgot_empty")
            ]);
        }

        if(!!$this->isExpired($forgotPassowrd->expired_at)) {
            return $this->fail([
                    "message" => Lang::get("notify.token_expired")
            ]);
        }

        return $this->success();
    }

    /**
     * fn reset password
     * @param unknown $request
     * @return object|StdClass
     */
    public function resetPassword ($request) {
        $validator = $this->validation->resetPassword( $request );
        // check validation
        if (!empty( $validator )) {
            $error = (object)$validator->toArray();
            $message = !empty($error->new_password) ? $error->new_password[0] : $error->confirm_password[0];
            return $this->fail ( [
                    "message" => $message
            ]);
        }

        $forgotPassword = $this->forgotPasswordRepo->findByField('token', $request->get('token_reset', ''))->first();
        // check empty token
        if(empty($forgotPassword)) {
            return $this->fail([
                    "message" => Lang::get("notify.token_forgot_empty")
            ]);
        }
        // check expired of token
        if(!!$this->isExpired($forgotPassword->expired_at)) {
            return $this->fail([
                    "message" => Lang::get("notify.token_expired")
            ]);
        }
        // change password
        $result = $this->authRepo->update([
                "password" => Hash::make($request->get('new_password'))
        ], $forgotPassword->user_id);

        // delete forgot password
        $this->forgotPasswordRepo->delete($forgotPassword->id);
        return $this->success($result);
    }

    /**
     * fn check username already exist
     * @param unknown $request
     * @return object|StdClass
     */
    public function checkUsername ($request) {
        if(!$request->has('username')) {
            return $this->fail(Lang::get('notify.username_empty'));
        }

        $user = $this->authRepo->findByField('username', $request->get('username'))->first();
        if(!empty($user)) {
            return $this->fail(Lang::get('notify.username_already_exist'));
        }

        return $this->success($user);
    }
}