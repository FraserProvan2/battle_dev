<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $appends = ['username'];

    function getUsernameAttribute() {
        return User::find($this->user_id)->name;
    }

}
