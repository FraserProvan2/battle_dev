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
            $this->constructTurn($request);
        } catch (Exception $e) {
            // catch any errors validating and constructing game engine
            return response()->json([
                'message' => 'Bad Request',
            ], 400);
        }

        // 2a. if not turn ender, just return after action update
        if (!$this->turn_ender) {
            $this->turn->update();
            $response = response()->json([
                'message' => 'action received',
            ]);
        }
        // 2b. calc actions, end round, prepare next turn
        else {
            // process and calculate players actions
            $this->calcPlayerActions();

            // update previous Turn
            $this->updateBattleFrame();
            $this->turn->status = "complete";
            $this->turn->update();

            // prepare next turn
            $new_turn = new Turn([
                'battle_id' => $this->battle->id,
                'turn_number' => $this->turn->turn_number + 1,
                'battle_frame' => $this->battle_frame,
            ]);
            $new_turn->save();

            $response = response()->json([
                'message' => 'action received, turn ended',
            ]);
        }

        // 3. broadcast event, return HTTP response
        TurnEndUpdate::dispatch($this->turn);
        return $response;
    }

    /*----------------------------------------------------------------------
    | Instance creation and mutation methods
    |----------------------------------------------------------------------*/

    /**
     * Validates and constructs the Turn/Battle objects
     * misc setters performed here as __construct doesn't have
     * access to the Auth middlewar. Serveral checks to verify and set 
     * data for the turn, whether the user can perform action in Post request.
     *
     * @param Request request
     * @return Boolean whether battle setup was successful or not
     */
    public function constructTurn(Request $request)
    {
        // validate request
        $request->validate([
            'battle' => 'required',
            'action' => 'required',
        ]);

        // set globals for battle instance (Battle/Turn)
        $this->player = Auth::user();
        $this->action = $request->action;
        $this->battle = Battle::find($request->battle);
        $this->turn = $this->battle->getTurn();

        // generate battle frame if round 1
        if ($this->turn->turn_number === 1) {
            $this->generateBattleFrame();
        }

        // set $this->player_role
        if ($this->player->id === $this->battle->user_a) {
            $this->player_role = "a";
        } else if ($this->player->id === $this->battle->user_b) {
            $this->player_role = "b";
        }

        // check if the current action with end turn or not
        if (!$this->turn->player_a_action && !$this->turn->player_b_action) {
            $this->turn_ender = false;
        } else if (!$this->turn->player_a_action && $this->turn->player_b_action ||
                    $this->turn->player_a_action && !$this->turn->player_b_action) {
            $this->turn_ender = true;
        } else {
            throw new Exception("Error setting frame");
        }

        // set players action this turn, catch if already actioned this turns
        if ($this->player_role === 'a') {
            // check if user has already actioned
            if (!$this->battle->getTurn()->player_a_action) {
                $this->turn->player_a_action = $this->action;
            } else {
                throw new Exception("User already actioned this turn");
            }
        } else if ($this->player_role === 'b') {
            // check if user has already actioned
            if (!$this->battle->getTurn()->player_b_action) {
                $this->turn->player_b_action = $this->action;
            } else {
                throw new Exception("User already actioned this turn");
            }
        }
    }

    /**
     * Generates a default battle frame, ready for round 1
     * 
     */
    public function generateBattleFrame()
    {
        // gets user A and Bs User by ID
        $player_a = User::find($this->battle->user_a);
        $player_b = User::find($this->battle->user_b);

        // default battle frame is set
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

        // sets this Turns battle_frame
        $this->turn->battle_frame = $this->battle_frame;
    }

    /**
     * Updates battle frame to reflect changes after the current round
     * 
     */
    public function updateBattleFrame()
    {
        $this->battle_frame = $this->battle->getTurn()->battle_frame;

        // update $this->battle_frame for recent calcs
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
            // dump("nothing happened...");
        }
        // both attack
        if ($action_a === 'attack' && $action_b === 'attack') {
            // player A
                // dump("player A attacked!");
                // dump("player A missed!");

            // player B
                // dump("player B attacked!");
                // dump("player B missed!");
        }
        // one block/one attack
        if ($action_a === 'attack' && $action_b === 'block' ||
            $action_a === 'block' && $action_b === 'attack'
        ) {
            // dump("player A's blocked and healed");
        }
    }
}
