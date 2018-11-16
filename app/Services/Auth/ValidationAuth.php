<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Validator;

class ValidationAuth {
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
    public function loginUser($request) {
        $validator = Validator::make ( $request->all (), [
                'username' => 'required',
                'password' => 'required|string'
        ] );

        if ($validator->fails ()) {
            return $validator->errors ();
        }
    }
    public function uploadCSV($request) {
        $validator = Validator::make ( $request->all (), [
                'file' => 'required|mimes:xls,xlsx',
                'file_terminated' => 'required',
                'file_enclosed' => 'required'
        ] );

        if ($validator->fails ()) {
            return $validator->errors ();
        }
    }
    public function forgotPassword($request) {
        $validator = Validator::make ( $request->all (), [
                'username' => 'required',
        ] );

        if ($validator->fails ()) {
            return $validator->errors ();
        }
    }
    public function resetPassword ($request) {
        $validator = Validator::make ( $request->all (), [
                'new_password' => 'required',
                'confirm_password' => 'required|same:new_password'
        ] );

        if ($validator->fails ()) {
            return $validator->errors ();
        }
    }
}