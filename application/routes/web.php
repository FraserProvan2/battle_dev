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
Route::get('battle/check', 'GameLobbyController@getBattleData');
Route::get('battle/dispatch/{turn_id}', 'GameLobbyController@dispatchBattleData');
    
    // Invites
    Route::get('invites/dispatch', 'GameLobbyController@dispatchInviteList');
    Route::get('invites/post', 'GameLobbyController@postInvite');
    Route::get('invites/cancel', 'GameLobbyController@cancelInvite');
    Route::get('invites/accept/{invite_id}', 'GameLobbyController@acceptInvite');

    
