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

// Player Actions/Battle progression
Route::post('battle', 'GameEngineController@main');

// Battle lobby
Route::get('battle/check', 'GameLobbyController@checkIfInBattle');
    
    // Invites
    Route::get('invites', 'GameLobbyController@getAll');
    Route::get('invites/post', 'GameLobbyController@postInvite');
    Route::get('invites/cancel', 'GameLobbyController@cancelInvite');
    Route::get('invites/accept/{invite_id}', 'GameLobbyController@acceptInvite');

    
