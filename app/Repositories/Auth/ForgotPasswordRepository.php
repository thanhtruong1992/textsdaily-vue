<?php

namespace App\Repositories\Auth;

use App\Repositories\BaseRepository;

class ForgotPasswordRepository extends BaseRepository implements IForgotPasswordRepository {
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\ForgotPassword";
    }
}