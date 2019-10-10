<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Battle;

// Auth for Battle channels
Broadcast::channel('App.Battle.{id}', function ($user, $id) {
    $battle = Battle::findOrNew($id);
    
    // IF user id is UserA OR UserB in Battle
    if ($user->id === $battle->user_a || $user->id === $battle->user_b ) {
        return true;
    }

    return false;
});

