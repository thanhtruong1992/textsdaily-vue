<?php

namespace App\Repositories\Auth;

use App\Repositories\BaseRepository;


class TokenRepository extends BaseRepository implements ITokenRepository {

    /**
	 * Specify Model class name
	 *
	 * @return string
	 */
	function model() {
		return "App\\Models\\Token";
	}
}