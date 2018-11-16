<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;

class UserTest extends TestCase
{
    /**
     * A basic test login.
     *
     * @return void
     */
    public function testLogin()
    {
        $params = [
            'username' => 'admin@success-ss.com.vn',
            'password' => '123456'
        ];

        $response = $this->json('POST', env('APP_URL') . '/login', $params);

        $user = Auth::user();

        $this->assertEquals($params['username'], $user->username);

        $response->assertRedirect('admin/dashboard');
    }
}
