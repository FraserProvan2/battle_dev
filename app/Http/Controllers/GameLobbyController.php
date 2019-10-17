<?php

namespace App\Http\Controllers;

use App\Battle;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameLobbyController extends Controller
{
    /**
     * Checks if user is in a battle or not
     * 
     * @return void
     */
    public function tryGetBattle() 
    {
        // return battle id if user is in battle
        if (User::getBattle()) {
            return User::getBattle()->id;
        }
    }
}
