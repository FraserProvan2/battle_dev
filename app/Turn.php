<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    protected $fillable = ['battle_id', 'turn_number', 'battle_frame', 'player_a_action', 'player_b_action'];

    protected $casts = [
        'battle_frame' => 'array'
    ];
}
