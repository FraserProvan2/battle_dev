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
    // Globals for Turn/Battle instance
    public $player;
    public $player_role; // if players A or B
    public $battle;
    public $battle_frame;
    public $turn;
    public $turn_ender; // whether this action will end the turn or not
    public $action; // 'attack' or 'block'

    // Calc Variables
    public $player_a_frame;
    public $player_b_frame;

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
            // calc and finish current turn
            $this->calcPlayerActions();
            $this->turn->status = "complete";
            $this->turn->update();
            $this->battle_frame['turn_summary'] = "";  // reset turn_summary

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
        // else get previous turns battle frame
        else {
            $this->battle_frame = $this->battle->getTurn()->battle_frame;
        }

        // set $this->player_role
        if ($this->player->id === $this->battle->user_a) {
            $this->player_role = "a";
        } else if ($this->player->id === $this->battle->user_b) {
            $this->player_role = "b";
        }

        // check if the current action will end turn or not
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

    /*----------------------------------------------------------------------
    | Battle methods
    |----------------------------------------------------------------------*/

    public function calcPlayerActions()
    {
        // players
        $this->player_a_frame = $this->battle_frame['player_a'];
        $this->player_b_frame = $this->battle_frame['player_b'];

        // players action
        $action_a = $this->turn->player_a_action;
        $action_b = $this->turn->player_b_action;

        // CASE 1: both block
        if ($action_a === 'block' && $action_b === 'block') {
            $this->battle_frame['turn_summary'] = "Both players blocked, nothing happened...";
        }
        // CASE 2: both attack
        if ($action_a === 'attack' && $action_b === 'attack') {
            // if player A is faster
            if ($this->player_a_frame['stats']['speed'] >= $this->player_b_frame['stats']['speed']) {
                $this->tryAttack('a'); // A attacks first
                $this->tryAttack('b');
            } 
            // else player B is faster
            else {
                $this->tryAttack('b'); // B attacks first
                $this->tryAttack('a');
            }
        }
        // CASE 3: one blocks, one attacks
        if ($action_a === 'attack' && $action_b === 'block' ||
        $action_a === 'block' && $action_b === 'attack'
        ) {
            // dump("player A's blocked and healed");
        }

        // update instance battle_frame of calculation results  
        $this->battle_frame['player_a'] = $this->player_a_frame;
        $this->battle_frame['player_b'] = $this->player_b_frame;

        // update battle frame on Turn object
        $this->turn->battle_frame = $this->battle_frame;
    }

    public function tryAttack($player)
    {
        // roll attack (if false attack missed)
        $attack_hit = true; // default: hit
        $roll = rand(1, 3);
        if ($roll === 1) { // 1/3 chance of missing
            $attack_hit = false; // miss
        }
        
        // player A attacks
        if ($player === 'a') {
            if ($attack_hit) {
                $this->player_b_frame['stats']['hp'] = ($this->player_b_frame['stats']['hp'] - $this->player_a_frame['stats']['damage']); // calc hp after damage
                $this->battle_frame['turn_summary'] .= $this->player_a_frame['username'] . " attacked for " . $this->player_a_frame['stats']['damage'] ." damage!\r\n";
            } else {
                $this->battle_frame['turn_summary'] .= $this->battle_frame['player_a']['username'] . " attacked and missed!\r\n"; // player A miss
            }
        } 
        // player B attacks
        else if ($player === 'b') {
            if ($attack_hit) {
                $this->player_a_frame['stats']['hp'] = ($this->player_a_frame['stats']['hp'] - $this->player_b_frame['stats']['damage']); // calc hp after damage
                $this->battle_frame['turn_summary'] .= $this->player_b_frame['username'] . " attacked for " . $this->player_b_frame['stats']['damage'] ." damage!\r\n";
            } else {
                $this->battle_frame['turn_summary'] .= $this->battle_frame['player_b']['username'] . " attacked and missed!\r\n"; // player A miss
            }
        }
    }

}
