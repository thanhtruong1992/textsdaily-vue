<?php

namespace App\Repositories\Auth;

use App\Repositories\Auth\IAuthenticationRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\User;

class AuthenticationRepository extends BaseRepository implements IAuthenticationRepository {
	/**
	 * Specify Model class name
	 *
	 * @return string
	 */
	function model() {
		return "App\\Models\\User";
	}

	public function findViaDB ( $userId ) {
	    return \DB::table('users')->find($userId);
	}

	public function addNewSenderList($userId, $sender) {
	    try {
	        $user = $this->find($userId);
	        $user->sender = $sender;
	        $user->save();
	    } catch (\Exception $e) {
	        return false;
	    }
	    return true;
	}
}