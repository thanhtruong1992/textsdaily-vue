<?php

namespace App\Services;

use Illuminate\Http\Request;
use Validator;

class ValidationService {
	/**
	 *
	 * @param Request $request        	
	 */
	public function registerUser($request) {
		$validator = Validator::make ( $request->all (), [ 
				'name' => 'required|string|max:255',
				'email' => 'required|string|email|max:255|unique:users',
				'password' => 'required|string|min:6' 
		] );
		
		if ($validator->fails ()) {
			return $validator->errors ();
		}
	}
}