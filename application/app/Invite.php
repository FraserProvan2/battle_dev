<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $appends = ['host'];

    function getHostAttribute() {
        return $this->user->name;
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
