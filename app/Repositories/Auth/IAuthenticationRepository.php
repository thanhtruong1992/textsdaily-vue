<?php

namespace App\Repositories\Auth;

interface IAuthenticationRepository {
    public function addNewSenderList($userId, $sender);
}