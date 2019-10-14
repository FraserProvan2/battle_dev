<?php

namespace App;

class PlayerFrame
{
    /**
     * This class represents players inside a battle frame,
     * the battle frame (battle_frame json in Turn) represents
     * the players stats in that turn (instance). 
     * 
     */

    public $username;
    public $damage;
    public $speed;
    public $hp;

    public function __construct(Array $player_stats)
    {
        $this->username = $player_stats['username'];
        $this->damage = $player_stats['damage'];
        $this->speed = $player_stats['speed'];
        $this->hp = $player_stats['hp'];
    }

    public function takeDamage(Int $damage)
    {
        $this->hp = $this->hp - $damage;
    }

    public function restoreHp(Int $hp)
    {
        $this->hp = $this->hp + $hp;
    }



}
