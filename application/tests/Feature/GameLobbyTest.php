<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameLobbyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function check_if_in_battle_returns_expected()
    {
        // setup
        $this->seedDB();
        $this->signInUser(User::find(3));

        $response = $this->get('/battle/check')
            ->assertOk();

        // ...

    }

    /** @test */
    public function get_all_invites_returns_invites()
    {
        // setup
        $this->seedDB();

        $response = $this->get('/invites')
            ->assertOk();

        $invites = $response->getData();

        $this->assertCount(5,  $invites); // expected amount from seed

        // expected first record
        $this->assertEquals($invites[0]->id, 1);
        $this->assertEquals($invites[0]->user_id, 3);
        $this->assertObjectHasAttribute('username', $invites[0]);
    }

    public function posts_invite_as_expected()
    {
        //
    }

    public function cant_post_invite_if_already_invite()
    {
        //
    }

    public function cancels_invite_as_expected()
    {
        //
    }

    public function invite_accpted_as_expected()
    {
        //
    }
}
