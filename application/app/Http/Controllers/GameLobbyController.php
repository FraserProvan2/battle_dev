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
     * Post game invite
     * 
     */
    public function postInvite()
    {
        $user = Auth::user();

        // check if user already has invite
        $check_for_current = Invite::where('user_id', $user->id)->first();
        if ($check_for_current) {
            return response()->json([
                'message' => 'User already has Invite',
            ], 400);
        }

        // create invite
        Invite::create(['user_id' => $user->id]);

        $this->dispatchInviteList();
    }

    /**
     * Cancels users game invite
     * 
     */
    public function cancelInvite()
    {
        Invite::where('user_id', Auth::user()->id)->first()
            ->delete();

        $this->dispatchInviteList();
    }

    /**
     * Accept game invite
     * 
     * @param Int invite id
     **/
    public function acceptInvite($id)
    {
        $invite = Invite::find($id);
        $accepted_user = Auth::user();

        // check inv still valid
        if (!$invite) {
            return response()->json([
                'message' => 'Invite expired',
            ], 400);
        }
        
        // check either user is in battle
        $battle = Battle::where('user_a', $invite->user_id)
            ->orWhere('user_a', $accepted_user->id)
            ->orWhere('user_b', $invite->user_id)
            ->orWhere('user_b', $accepted_user->id)
            ->first();
        if ($battle) {
            return response()->json([
                'message' => 'One of the users is currently in battle',
            ], 400);
        }
        // create battle
        $new_battle = Battle::create([
            'user_a' => $invite->user_id,
            'user_b' => $accepted_user->id
        ]);
        $new_battle->startBattle();

        // delete accepted invite
        $invite->delete();

        // see if accepting user has invite, delete this one also
        $other_users_invite = Invite::where('user_id', $accepted_user->id)->first();
        if ($other_users_invite) {
            $other_users_invite->delete();
        }
    }
}
