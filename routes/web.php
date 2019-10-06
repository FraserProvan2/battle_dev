<?php

/*----------------------------------------------------------------------
| Dashboard
|----------------------------------------------------------------------*/

Route::get('/', 'DashboardController@index')->name('dashboard');

/*----------------------------------------------------------------------
| Auth Routes
|----------------------------------------------------------------------*/

Route::get('login/github', 'Auth\GithubLoginController@redirectToProvider')->name('github-login');
Route::get('login/github/callback', 'Auth\GithubLoginController@handleProviderCallback');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

