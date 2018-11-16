<?php

namespace App\Services\Auth;

interface ITokenService {
    public function createToken();
    public function getTokenByUserId();
    public function getTokenByToken($token);
    public function updateExpriredTime($attributes, $id);
}