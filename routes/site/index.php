<?php

Route::group(['prefix' => 'feedback'], function () {
    Route::post('/', 'API\FeedbackController@create');
});

Route::group(['prefix' => 'menus'], function () {
    Route::get('/', 'API\MenusController@activeMenus');
    Route::get('/{slug}', 'API\MenusController@activeMenu');
});

Route::group(['prefix' => 'posts'], function () {
    Route::get('/{group}', 'API\PostsController@activePosts');
    Route::get('/detail/{slug}', 'API\PostsController@activePost');
});

Route::group(['prefix' => 'settings'], function () {
    Route::get('/{setting}', 'API\SettingsController@activeSetting');
});

Route::group(['prefix' => 'languages'], function () {
    Route::get('/', 'API\SettingsController@languages');
});