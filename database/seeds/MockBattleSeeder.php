<?php

use App\Battle;
use App\Turn;
use Illuminate\Database\Seeder;

class MockBattleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // creates 2 users
        factory(App\User::class, 2)->create();

        // creates battle instance
        $battle = Battle::create([
            'user_a' => 1,
            'user_b' => 2,
        ]);

        // sets round 0
        Turn::create(['battle_id' => $battle->id]);
    }
}
