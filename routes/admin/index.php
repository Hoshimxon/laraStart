<?php

//Route::post('register', 'Auth\RegisterController@create');

Route::post('auth/login', 'AuthController@login');

Route::middleware('auth:api')->group( function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::post('logout','AuthController@logout');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'API\UsersController@all');
        Route::get('/{user}', 'API\UsersController@show');
        Route::post('/', 'API\UsersController@create');
        Route::put('/', 'API\UsersController@changeStatus');
        Route::delete('/{user}', 'API\UsersController@destroy');
    });

    Route::group(['prefix' => 'menus'], function () {
        Route::get('/', 'API\MenusController@index');
        Route::get('/{menu}', 'API\MenusController@show');
        Route::post('/', 'API\MenusController@create');
        Route::put('/{menu}', 'API\MenusController@update');
        Route::delete('/{menu}', 'API\MenusController@destroy');
    });

    Route::group(['prefix' => 'posts'], function () {
        Route::get('/{group}', 'API\PostsController@index');
        Route::get('/detail/{post}', 'API\PostsController@show');
        Route::post('/{group}', 'API\PostsController@create');
        Route::put('/{post}', 'API\PostsController@update');
        Route::delete('/{post}', 'API\PostsController@destroy');
    });

    Route::group(['prefix' => 'feedback'], function () {
        Route::get('/', 'API\FeedbackController@index');
        Route::get('/{feedback}', 'API\FeedbackController@show');
        Route::delete('/{feedback}', 'API\FeedbackController@destroy');
    });

    Route::group(['prefix' => 'settings'], function () {
        Route::get('/', 'API\SettingsController@index');
        Route::get('/{setting}', 'API\SettingsController@show');
        Route::post('/', 'API\SettingsController@create');
        Route::put('/{setting}', 'API\SettingsController@update');
        Route::delete('/{setting}', 'API\SettingsController@destroy');
    });

});