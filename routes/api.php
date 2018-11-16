<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'prefix'     => '',
    'middleware' => 'auth.api'
], function() {
    Route::post('/sms', 'Api\SmsController@addSMS');
    Route::get('/sms/report/{uuid}', 'Api\SmsController@getReport');
});

// Route::group(['prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function () {
//     Route::get('example', 'CompaniesController', ['except' => ['create', 'edit']]);
// });

Route::group(['prefix' => '/v1', 'as' => 'api.'], function () {
    Route::post('/login', 'Auth\LoginController@login');
});