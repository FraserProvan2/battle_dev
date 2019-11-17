<?php

namespace App\Http\Controllers;

use App\Battle;
use App\Events\AnnounceWinner;
use App\Events\TurnEndUpdate;
use App\PlayerFrame;
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
    public $turn;
    public $battle_frame;
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
            // calc and finish current turn
            $this->calcPlayerActions();
            $this->turn->status = "complete";
            $this->turn->update();
            $this->battle_frame['turn_summary'] = []; // reset turn_summary

            // end game is player has won
            if ($this->checkForWinner()) {

                // end battle
                $this->endBattle();
                return response()->json([
                    'message' => 'game end.',
                ]);
            }

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
        TurnEndUpdate::dispatch($this->battle->getTurn());
        return $response;
    }

    /*----------------------------------------------------------------------
    | Instance creation and mutation methods
    |----------------------------------------------------------------------*/

    /**
     * Validates and constructs the Turn/Battle objects
     * misc setters performed here as __construct doesn't have
     * access to the Auth middleware. Serveral checks to verify and set
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
        try {
            $this->player = Auth::user();
            $this->action = $request->action;
            $this->battle = Battle::find($request->battle);
            $this->turn = $this->battle->getTurn();
            $this->battle_frame = $this->battle->getTurn()->battle_frame;

            // set $this->player_role
            if ($this->player->id == $this->battle->user_a) {
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
            }
        } catch (Exception $e) {
            throw new Exception("Error setting globals");
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
                'avatar' => $player_a->avatar,
                'speed' => $player_a->speed(),
                'damage' => $player_a->damage(),
                'hp' => $player_a->hp(),
            ],
            'player_b' => [
                'username' => $player_b->name,
                'avatar' => $player_b->avatar,
                'speed' => $player_b->speed(),
                'damage' => $player_b->damage(),
                'hp' => $player_b->hp(),
            ],
        ];

        // sets this Turns battle_frame
        $this->turn->battle_frame = $this->battle_frame;
    }

    /*----------------------------------------------------------------------
    | Battle methods
    |----------------------------------------------------------------------*/

    // contains players data for THIS battle frame (this turn)
    public $player_a_frame;
    public $player_b_frame;

    /**
     * This is the master function that determines what happens
     * with the players actions, and calculates/updates hp afterwards
     *
     */
    public function calcPlayerActions()
    {
        // create new PlayerFrame for each player
        $this->player_a_frame = new PlayerFrame($this->battle_frame['player_a']);
        $this->player_b_frame = new PlayerFrame($this->battle_frame['player_b']);

        // players action
        $action_a = $this->turn->player_a_action;
        $action_b = $this->turn->player_b_action;

        // CASE 1: both block
        if ($action_a === 'block' && $action_b === 'block') {
            $this->addToTurnSummary("Both players blocked, nothing happened...");
        }
        // CASE 2: both attack
        if ($action_a === 'attack' && $action_b === 'attack') {
            // if player A is faster
            if ($this->player_a_frame->speed >= $this->player_b_frame->speed) {
                $this->tryAttack('a'); // A attacks first

                if (!$this->checkForWinner()) {
                    $this->tryAttack('b');
                }
            }
            // else player B is faster
            else {
                $this->tryAttack('b'); // B attacks first

                if (!$this->checkForWinner()) {
                    $this->tryAttack('a');
                }
            }
        }
        // CASE 3: one blocks, one attacks
        if ($action_a === 'attack' && $action_b === 'block' ||
            $action_a === 'block' && $action_b === 'attack'
        ) {
            // player A blocked
            if ($action_a === 'block') {
                $this->tryBlock('a');
            }
            // player B blocked
            else if ($action_b === 'block') {
                $this->tryBlock('b');
            }
        }

        // update battle_frame of calculation results
        $this->battle_frame['player_a'] = $this->player_a_frame;
        $this->battle_frame['player_b'] = $this->player_b_frame;

        // update battle frame on Turn object
        $this->turn->battle_frame = $this->battle_frame;
    }

    /**
     * Player rolls to attack opposition, chances of
     * hitting or missing. Then updates battle frame ($this->battle_frame)
     *
     * @param String a or b (Player)
     *
     */
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
                $this->player_b_frame->takeDamage($this->player_a_frame->damage); // player A attack
                $this->addToTurnSummary("{$this->player_a_frame->username} attacked for {$this->player_a_frame->damage} damage!");
            } else {
                $this->addToTurnSummary("{$this->player_a_frame->username} attacked and missed!"); // player A miss
            }
        }
        // player B attacks
        else if ($player === 'b') {
            if ($attack_hit) {
                $this->player_a_frame->takeDamage($this->player_b_frame->damage); // player B attack
                $this->addToTurnSummary("{$this->player_b_frame->username} attacked for {$this->player_b_frame->damage} damage!");
            } else {
                $this->addToTurnSummary("{$this->player_b_frame->username} attacked and missed!"); // player B miss
            }
        }
    }

    /**
     * Player rolls to block opponents attack, chances of
     * perfectly blocking (big heal), normal blocking (heal) or
     * failing to block, and getting critically attacked
     *
     *  @param String a or b (Player)
     */
    public function tryBlock($player)
    {
        $roll = rand(1, 10);

        // 1/10 for perfect block
        if ($roll === 1) {
            if ($player === 'a') {
                $this->player_a_frame->restoreHp($this->player_b_frame->damage); // restore opponents attack in HP
                $this->addToTurnSummary("{$this->player_a_frame->username} perfectly blocked! Restoring {$this->player_b_frame->damage} HP!");
            } else if ($player === 'b') {
                $this->player_a_frame->restoreHp($this->player_a_frame->damage); // restore opponents attack in HP
                $this->addToTurnSummary("{$this->player_b_frame->username} perfectly blocked! Restoring {$this->player_a_frame->damage} HP!");
            }
        }
        // 2-5 rolls normal block
        else if ($roll > 1 && $roll < 6) {
            if ($player === 'a') {
                $heal_amount = ($this->player_a_frame->hp / 8); // players hp / 8
                $this->player_a_frame->restoreHp($heal_amount);
                $this->addToTurnSummary("{$this->player_a_frame->username} blocked! Restoring {$heal_amount} HP!");
            } else if ($player === 'b') {
                $heal_amount = ($this->player_b_frame->hp / 8); // players hp / 8
                $this->player_b_frame->restoreHp($heal_amount);
                $this->addToTurnSummary("{$this->player_b_frame->username} blocked! Restoring {$heal_amount} HP!");
            }
        }
        // 5-10 rolls fail to block
        else if ($roll > 5) {
            if ($player === 'a') {
                $damage_amount = ($this->player_b_frame->damage * 2); // opponents damage * 2
                $this->player_a_frame->takeDamage($damage_amount);
                $this->addToTurnSummary("{$this->player_a_frame->username} block failed! {$this->player_b_frame->username} critically attacks for {$damage_amount} damage!");
            } else if ($player === 'b') {
                $damage_amount = ($this->player_a_frame->damage * 2); // opponents damage * 2
                $this->addToTurnSummary("{$this->player_b_frame->username} block failed! {$this->player_a_frame->username} critically attacks for {$damage_amount} damage!");
            }
        }
    }

    /**
     * Check if either player has gone below 0 hp
     *
     * @return String a or b (Player)
     *
     */
    public function checkForWinner()
    {
        if ($this->player_a_frame->hp <= 0) {
            return 'b';
        } else if ($this->player_b_frame->hp <= 0) {
            return 'a';
        }
    }

    /**
     * Ends the battle, assigns the players W/L,
     * Deletes the battle + turn
     *
     */
    public function endBattle()
    {
        // figures out winner
        if ($this->checkForWinner() === 'a') {
            $winner = $this->battle->user_a;
            $loser = $this->battle->user_b;
        } else if ($this->checkForWinner() === 'b') {
            $winner = $this->battle->user_b;
            $loser = $this->battle->user_a;
        }

        // assign Win
        $user_winner = User::find($winner);
        $user_winner->wins = ($user_winner->wins + 1);
        $user_winner->save();

        // assign Loss
        $user_loser = User::find($loser);
        $user_loser->losses = ($user_loser->losses + 1);
        $user_loser->save();

        // send final battle updates
        TurnEndUpdate::dispatch($this->turn); // announce winner
        AnnounceWinner::dispatch($user_winner->name, $this->battle);

        // delete battle + turns
        Battle::find($this->battle->id)->delete();
    }

    /**
     * Adds log to Turn Summary
     *
     * @param String log
     */
    public function addToTurnSummary($log)
    {
        array_push($this->battle_frame['turn_summary'], $log);
    }
}
