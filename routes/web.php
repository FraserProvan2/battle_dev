<?php

/*----------------------------------------------------------------------
| Dashboard
|----------------------------------------------------------------------*/

Route::get('/', 'DashboardController@index')->name('dashboard');

/*----------------------------------------------------------------------
| Auth Routes
|----------------------------------------------------------------------*/

Route::get('login/github', 'Auth\AccountController@redirectToProvider')->name('github-login');
Route::get('login/github/callback', 'Auth\AccountController@handleProviderCallback');
Route::post('logout', 'Auth\AccountController@logout')->name('logout');

/*----------------------------------------------------------------------
| Battle Routes
|----------------------------------------------------------------------*/

Route::post('battle', 'GameEngineController@main');
