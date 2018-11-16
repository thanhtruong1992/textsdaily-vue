<?php

Route::group(['prefix' => '/', 'as' => 'admin.'], function () {
    Route::post('/login', 'Admin\LoginController@login');

    Route::group([
        'prefix' => '',
        'middleware' => 'auth.admin'
    ], function() {
        Route::get('/me', 'Admin\LoginController@getMe');
    });
});