<?php

use App\Invite;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;

class InviteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(App\User::class, 5)->create();

        foreach($users as $user) {
            Invite::create(['user_id' => $user->id]);
        }
    }
}
