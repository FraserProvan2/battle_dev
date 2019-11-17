<?php

namespace Tests\Unit\Battle;

use App\Battle;
use App\Http\Controllers\GameEngineController;
use App\Turn;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ConstructTurnTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function turn_constructs_as_expected()
    {
        // setup
        $this->seedDB();
        $this->signInUser(User::find(1));
        $game_engine = new GameEngineController;

        $game_engine->constructTurn(new Request(['battle' => '1', 'action' => 'attack']));
        $current_turn = $game_engine->battle->getTurn();

        // action updated shows turn was constructed successfully
        $this->assertEquals($current_turn->player_a_action, 'attack');
    }

    /** @test */
    public function request_fails_without_required_data()
    {
        // setup
        $this->seedDB();
        $this->signInUser(User::find(1));
        $game_engine = new GameEngineController;

        // correct exception thrown on turn construction
        $this->expectExceptionMessage('The given data was invalid.');
        $game_engine->constructTurn(new Request());
    }

    /** @test */
    public function cannont_construct_if_no_auth()
    {
        // setup
        $this->seedDB();
        $game_engine = new GameEngineController;

        // correct exception thrown on turn construction
        $this->expectExceptionMessage('Error setting globals');
        $game_engine->constructTurn(new Request(['battle' => '1', 'action' => 'attack']));
    }

    /** @test */
    public function ensure_all_globals_requried_for_instance_are_set()
    {
        // setup
        $this->seedDB();
        $this->signInUser(User::find(1));

        // construct turn
        $game_engine = new GameEngineController;
        $game_engine->constructTurn(new Request(['battle' => '1', 'action' => 'attack']));

        $battle = Battle::find(1);

        // this is to set action, as the object hasnt been updated for the action selected in the reqeust
        $battle->getTurn()->player_a_action = 'attack';

        // assert battle and turn attributes are instance of models
        $this->assertInstanceOf('App\Turn', $game_engine->turn);
        $this->assertInstanceOf('App\Battle', $game_engine->battle);

        // assert other globals are as expected
        $this->assertEquals($game_engine->turn_ender, false);
        $this->assertEquals($game_engine->player, User::find(1));
        $this->assertEquals($game_engine->player_role, 'a');
        $this->assertEquals($game_engine->action, 'attack');
    }

    /** @test */
    public function game_ender_set_as_true_when_expected()
    {
        // setup
        $this->seedDB();
        $this->signInUser(User::find(1));

        // mock player B action
        $turn = Turn::find(1);
        $turn->player_b_action = 'attack';
        $turn->save();

        // construct turn
        $game_engine = new GameEngineController;
        $game_engine->constructTurn(new Request(['battle' => '1', 'action' => 'attack']));

        $this->assertEquals($game_engine->turn_ender, true);
    }

    /** @test */
    public function player_thats_already_actioned_gets_404()
    {
        // setup
        $this->seedDB();
        $this->signInUser(User::find(1));

        // player A first action
        $this->post('battle', [
            'battle' => 1,
            'action' => 'attack',
        ]);

        // attempt player A second action
        $response = $this->post('battle', [
            'battle' => 1,
            'action' => 'attack',
        ]);

        $response->assertStatus(400);
    }
}
