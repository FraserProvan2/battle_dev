<?php

Route::get('/', 'LandingPageController@index')->name('landing');
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

/*----------------------------------------------------------------------
| Auth Routes
|----------------------------------------------------------------------*/

Route::get('login/github', 'Auth\LoginController@redirectToProvider')->name('github-login');
Route::get('login/github/callback', 'Auth\LoginController@handleProviderCallback');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
