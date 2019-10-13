<?php

namespace App\Http\Controllers;

use App\Battle;
use App\Events\TurnEndUpdate;
use App\Turn;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameEngineController extends Controller
{
    public $player;
    public $player_role; // if players A or B
    public $battle;
    public $battle_frame;
    public $turn;
    public $turn_ender; // whether this action will end the turn or not
    public $action; // 'attack' or 'block'

    /**
     * Main class method, processes fight round, 
     * game logic + execution of battle
     *
     * @param Request $request
     */
    public function main(Request $request)
    {
        // 1. set up and validate battle Instance
        try {
            $this->setGlobals($request);
            $this->setFrame();
            $this->setPlayerAction(); // update Turn object for current plays action
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Bad Request',
            ], 400);
        }
        
        // 3a. if not turn ender, just return after action update
        if (!$this->turn_ender) {
            $this->turn->update();

            $response = response()->json([
                'message' => 'action received',
            ]);
        }
        // 3b. calc actions, end round, prepare next turn
        else { 
            // process and calculate players actions
            $this->calcPlayerActions();
            
            // update previous turn
            $this->updateBattleFrame();
            $this->turn->status = "complete";
            $this->turn->update();

            // prepare next turn
            $this->prepareNextTurn();

            $response = response()->json([
                'message' => 'action received, turn ended',
            ]);
        }

        // 4. broadcast event, return HTTP response
        TurnEndUpdate::dispatch($this->turn);
        return $response;
    }

    /*----------------------------------------------------------------------
    | Instance creation and mutation methods
    |----------------------------------------------------------------------*/

    /**
     * Sets up instance globals, and valdiates request
     * We do these here because constructor doesn't
     * have Auth avalible
     *
     * @param Request request
     *
     * @return Boolean whether battle setup was successful or not
     */
    public function setGlobals(Request $request)
    {
        // validate request
        $request->validate([
            'battle' => 'required',
            'action' => 'required',
        ]);

        // set globals for battle instance (Battle/Turn)
        $this->battle = Battle::find($request->battle);
        $this->player = Auth::user();
        $this->action = $request->action;
        $this->setPlayerRole();
    }

    public function setFrame()
    {
        $this->turn = $this->battle->getTurn();

        $action_a = $this->turn->player_a_action;
        $action_b = $this->turn->player_b_action;

        // if fresh game, generate battle frame
        if ($this->turn->turn_number === 1) {
            $this->setBattleFrame();
        }

        // check if user has already actioned
        if ($this->player_role === "a" && $this->turn->player_a_action || 
            $this->player_role === "b" && $this->turn->player_b_action) {
                throw new Exception("User already actioned this turn");
        }

        // fresh round
        if (!$action_a && !$action_b) {
            $this->turn_ender = false;
        } else if (!$action_a && $action_b || $action_a && !$action_b) {
            $this->turn_ender = true;
        } else {
            throw new Exception("Error setting frame");
        }
    }

    public function setBattleFrame()
    {
        $player_a = User::find($this->battle->user_a);
        $player_b = User::find($this->battle->user_b);

        $this->battle_frame = [
            'turn_summary' => '',
            'player_a' => [
                'username' => $player_a->name,
                'stats' => [
                    'speed' => $player_a->speed(),
                    'damage' => $player_a->damage(),
                    'hp' => $player_a->hp(),
                ],
            ],
            'player_b' => [
                'username' => $player_b->name,
                'stats' => [
                    'speed' => $player_b->speed(),
                    'damage' => $player_b->damage(),
                    'hp' => $player_b->hp(),
                ],
            ],
        ];

        $this->turn->battle_frame = $this->battle_frame;
    }

    public function setPlayerAction()
    {
        if ($this->player_role === 'a') {
            $this->turn->player_a_action = $this->action;
        } else if ($this->player_role === 'b') {
            $this->turn->player_b_action = $this->action;
        }
    }

    public function setPlayerRole()
    {
        if ($this->player->id === $this->battle->user_a) {
            $this->player_role = "a";
        } else if ($this->player->id === $this->battle->user_b) {
            $this->player_role = "b";
        }
    }

    public function updateBattleFrame() 
    {
        $this->battle_frame = $this->battle->getTurn()->battle_frame;
        dump($this->battle_frame);
        // update $this->battle_frame
    }


    public function prepareNextTurn()
    {
        $new_turn = new Turn([
            'battle_id' => $this->battle->id,
            'turn_number' => $this->turn->turn_number + 1,
            'battle_frame' => $this->battle_frame
        ]);
  
        $new_turn->save();
    }

    /*----------------------------------------------------------------------
    | Battle methods
    |----------------------------------------------------------------------*/

    public function calcPlayerActions()
    {
        $action_a = $this->turn->player_a_action;
        $action_b = $this->turn->player_b_action;

        // both block
        if ($action_a === 'block' && $action_b === 'block') {
            // $this->battle_frame['turn_summary'] = 'Nothing happened...';
            dump("nothing happened...");
        }
        // both attack
        if ($action_a === 'attack' && $action_b === 'attack') {
            // player A attacks
            if ($this->rollAttack()) {
                dump("player A attacked!");
            } else {
                dump("player A missed!");
            }

            // player B attacks
            if ($this->rollAttack()) {
                dump("player B attacked!");
            } else {
                dump("player B missed!");
            }
        }
        // one block/one attack
        if ($action_a === 'attack' && $action_b === 'block' ||
            $action_a === 'block' && $action_b === 'attack'
        ) {
            dump("rollAttack() & rollBock()");
        }
    }

    public function rollAttack()
    {
        if (rand(1, 10) > 5) {
            return true;
        }

        return false;
    }

    public function rollBlock()
    {
        //
    }

}
