<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    protected $fillable = ['battle_id', 'turn_number', 'battle_frame'];

    protected $casts = [
        'battle_frame' => 'array'
    ];
}
