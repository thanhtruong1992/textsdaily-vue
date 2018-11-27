<?php

Route::group(['prefix' => '/', 'as' => 'admin.'], function () {
    Route::post('/login', 'Admin\LoginController@login');

    Route::group([
        'prefix' => '',
        'middleware' => 'auth.admin'
    ], function() {
        Route::get('/me', 'Admin\LoginController@getMe');  
        Route::get('/dashboard', 'Admin\DashBoardController@dashboard');
        Route::get ('/login-with-other-role/{id}', "Admin\LoginController@loginWithOtherRole");
        Route::get ('/return-parent', "Admin\LoginController@loginWithParentRole" );

        // route client
        Route::group([
            'prefix' => 'client'
        ], function() {
            Route::get('/', 'Admin\ClientController@index');
            Route::get('/{id}', 'Admin\ClientController@get');
            Route::post('/', 'Admin\ClientController@store');
            Route::put('/', 'Admin\ClientController@update');
            Route::post('/delete', 'Admin\ClientController@delete');
        });
    });
});