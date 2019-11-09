<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    /**
     * Get the Turns for the Battle
     */
    public function turn()
    {
        return $this->hasMany('App\Turn');
    }

    public function getTurn()
    {
        return $this->turn->last();
    }

}
