<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Auth;
use App\Models\user;

class ClientTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateGroup2()
    {
        $params = [
            'name' => 'Unit Test',
            'username' => 'unitest123',
            'email' => 'unittest@success-ss.com.vn',
            'password' => 'Aa@123456',
            'password_confirmation' => 'Aa@123456',
            'country' => 'VN',
            'time_zone' => 'Asia/Saigon',
            'language' => 'en',
            'currency' => 'USD',
            'default_price_sms' => 0.2,
            'sender' => ['Verify'],
            'billing_type' => 'ONE_TIME'
        ];
        Auth::loginUsingId(1);
        $response = $this->json('POST', env('APP_URL') . '/agency/clients/create/account', $params);
        $client = User::orderBy('id', 'desc')->first();

        $this->assertEquals($params['email'], $client->email);
        $response->assertSessionHas('success');
        $response->assertRedirect('/agency/clients/create/account/' . $client->id);
        User::destroy($client->id);
    }
}
