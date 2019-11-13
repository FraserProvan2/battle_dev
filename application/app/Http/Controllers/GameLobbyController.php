<?php

namespace App\Http\Controllers;

use App\Battle;
use App\Events\InviteList;
use App\Events\TurnEndUpdate;
use App\Invite;
use App\Turn;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameLobbyController extends Controller
{
    /*----------------------------------------------------------------------
    | Lobby (Battle instance)
    |----------------------------------------------------------------------*/

    /**
     * Checks if user is in a battle or not, 
     * if so return battle ID, exception incase user isnt in 
     * battle.
     * 
     **/
    public function getBattleData() 
    {
        try {
            if (User::getBattle()) {
                return [
                    'battle' => User::getBattle(),
                    'turn' => User::getBattle()->getTurn()
                ];
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'User not in battle',
            ], 200);
        }
    }

    /**
     * Manually dispatchs event of last rounds data
     * 
     **/
    public function dispatchBattleData($turn_id)
    {
        TurnEndUpdate::dispatch(
            Turn::where('id', $turn_id)->first()
        );
    }

    /*----------------------------------------------------------------------
    | Invites
    |----------------------------------------------------------------------*/

    /**
     * Manually dispatch invite list events
     * 
     **/
    public function dispatchInviteList()
    {
        InviteList::dispatch();
    }

    /**
     * Accept game invite
     * 
     **/
    public function acceptInvite($id)
    {
        $invite = Invite::find($id);
        $accepted_user = Auth::user();

        dd($invite, $accepted_user);

        // TODO - also lock accept in frontend so cant be pressed if no auth
    }
}
