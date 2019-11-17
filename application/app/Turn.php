<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    protected $fillable = ['battle_id', 'turn_number', 'battle_frame', 'a_action', 'b_action'];

    protected $casts = [
        'battle_frame' => 'array'
    ];

    static public function createBattleFrame(User $a, User $b)
    {
        return [
            'turn_summary' => [],
            'player_a' => [
                'username' => $a->name,
                'avatar' => $a->avatar, 
                'speed' => $a->speed(),
                'damage' => $a->damage(),
                'hp' => $a->hp(),
            ],
            'player_b' => [
                'username' => $b->name,
                'avatar' => $b->avatar, 
                'speed' => $b->speed(),
                'damage' => $b->damage(),
                'hp' => $b->hp(),
            ],
        ];
    }
}
