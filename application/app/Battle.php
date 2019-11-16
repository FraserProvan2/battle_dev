<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    protected $fillable = ['user_a', 'user_b'];

    /**
     * Get the Turns for the Battle
     * 
     */
    public function turn()
    {
        return $this->hasMany('App\Turn');
    }

    /**
     * Gets previous turn
     * 
     */
    public function getTurn()
    {
        return $this->turn->last();
    }

    /**
     * Creates turn one
     * 
     */
    public function startBattle()
    {
        $player_a = User::find($this->user_a);
        $player_b = User::find($this->user_b);

        return Turn::create([
            'battle_id' => $this->id,
            'turn_number' => 1,
            'battle_frame' => Turn::createBattleFrame($player_a, $player_b)
        ]);
    }

}
