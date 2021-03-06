<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'github_id', 'avatar'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Gets battle data if users in battle
     *
     * @return Battle collection
     */
    public static function getBattle()
    {
        $user_id = auth()->id();

        $battle_id = DB::table('battles')
            ->where('user_a', $user_id)
            ->orWhere('user_b', $user_id)
            ->pluck('id');

        return Battle::where('id', $battle_id)->first();
    }

    public function speed()
    {
        return 10;
    }

    public function damage()
    {
        return 25;
    }

    public function hp()
    {
        return 125;
    }
}
